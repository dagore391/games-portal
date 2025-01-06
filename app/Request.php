<?php
namespace app;

use app\config\Constants;
use app\config\Paths;
use app\data\MethodTypesEnum;
use app\exception\RequestException;
use app\exception\SecurityException;
use app\model\MemberModel;
use app\model\VisitorModel;
use app\security\Auth;
use app\security\Captcha;
use app\security\Security;
use app\security\Session;
use app\security\Token;

final class Request {
	private static $_logInUser = null;
	private $_controller = null;
	private $_method = null;
	private $_urlParams = [];
	private static $_captcha = '';
	private static $_generateToken = false;
	private static $_generationstartTime = 0;
	
	public function __construct(?object $logInUser) {
		self::$_generationstartTime = microtime(true);
		self::$_logInUser = $logInUser;
		$this->_initVars();
	}
	
	public function callControllerMethod() : void {
		$controller = "\\app\\controller\\{$this->_controller}";
		$controllerFullPath = Paths::CONTROLLER . $this->_controller . '.php';
		// Se verifica que el fichero del controlador exista.
		if(!file_exists($controllerFullPath)) {
			throw new RequestException("No se ha podido crear el método invocable \"{$this->_method}\" porque el fichero \"{$controllerFullPath}\" no existe.");
		}
		// Se importa el fichero del controlador para crear una instancia del mismo para realizar el resto de comprobaciones.
		include_once $controllerFullPath;
		$controllerInstance = new $controller();
		// Se verifica que la clase del objeto instanciado sea de tipo Controller y contenga el método solicitado.
		if(!$controllerInstance instanceof Controller) {
			throw new RequestException("No se ha podido crear el método invocable \"{$this->_method}\" porque la clase que lo contiene no es de tipo \"Controller\".");
		} else if(!method_exists($controllerInstance, $this->_method)) {
			throw new RequestException("El método \"{$this->_method}\" de la clase \"{$controller}\" no existe.");
		}
		// Se verifica que el método solicitado sea de acceso público para que pueda ser invocado.
		$reflectionMethod = new \ReflectionMethod($controllerInstance, $this->_method);
		if(!$reflectionMethod->isPublic()) {
			throw new RequestException("El método \"{$this->_method}\" del controlador \"{$controller}\" no es público.");
		}
		// Se realizan las operaciones previas a la invocación del método en función de su tipo.
		$this->_methodPreExecution($this->_getMethodType($this->_method));
		// Se crea el context del controlador.
		$controllerInstance->setContext($this->_getDefaultContext($this->_controller, $this->_method));
		// Se generan los permisos de acceso a los métodos del controlador con un valor por defecto.
		$controllerInstance->setMethodsPermissions(Security::getFullControllerMethodsAccessPermissions($controllerInstance));
		// Se verifican los permisos de acceso al método solicitado.
		if(!Security::checkAccess($controllerInstance, $this->_method, self::$_logInUser?->account_group)) {
			throw new SecurityException("No dispone de permisos suficientes para acceder al método \"$this->_method\" del controlador \"$controller\".");
		}
		// Se invoca el método del controlador que se corresponda con la solicitud del cliente.
		call_user_func_array([$controllerInstance, $this->_method], $this->_urlParams);
		// Se realizan las operaciones tras la invocación del método en función de su tipo.
		$this->_methodPostExecution($controllerInstance, $this->_getMethodType($this->_method), $this->_controller);
	}
	
	private static function _getDefaultContext(string $controllerName, string $methodName, bool $generateToken = false) : array {
		// Datos del cliente.
		$pageId = "{$controllerName}-{$methodName}";
		$clientBrowser = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : Constants::UNKNOWN_BROWSER;
		$clientIp = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : Constants::UNKNOWN_IP;
		// Se actualiza la visita
		self::_saveVisitor($clientIp, $clientBrowser, $pageId);
		// Se preparan los mensajes
		$error_messages = Session::exists(Constants::SESSION_ERROR_MESSAGES) ? Session::get(Constants::SESSION_ERROR_MESSAGES) : array();
		$success_messages = Session::exists(Constants::SESSION_SUCCESS_MESSAGES) ? Session::get(Constants::SESSION_SUCCESS_MESSAGES) : array();
		$warning_messages = Session::exists(Constants::SESSION_WARNING_MESSAGES) ? Session::get(Constants::SESSION_WARNING_MESSAGES) : array();
		$messages_html = self::generateMessagesHtml($error_messages, 'error');
		$messages_html .= self::generateMessagesHtml($success_messages, 'success');
		$messages_html .= self::generateMessagesHtml($warning_messages, 'warning');
		// Se prepara el $context por defecto del controlador.
		return [
			'META' => [
				'CHARSET' => Constants::SITE_ENCODING,
				'VIEWPORT' => '',
				'DESCRIPTION' => '',
				'KEYWORDS' => '',
				'AUTHOR' => '',
				'NAME' => Constants::SITE_NAME,
				'TITLE' => Constants::SITE_NAME,
				'EMAIL' => Constants::SITE_EMAIL,
				'ENGINE' => Constants::SITE_ENGINE,
				'COPYRIGHT_MESSAGE' => Constants::SITE_COPYRIGHT_HTML
			],
			'CLIENT' => [
				'IP' => $clientIp,
				'REMOTE_PAGE' => $pageId,
				'BROWSER' => $clientBrowser,
				'LOGIN_USER' => self::$_logInUser,
				'LOGIN_USER_TOTAL_UNREAD_PM' => Auth::isAuthenticated() ? MemberModel::getTotalUnreadMessages(self::$_logInUser->id) : 0
			],
			'PAGE' => [
				'GENERATION_TIME' => 0
			],
			'MESSAGES' => [
				'ERROR' =>  $error_messages,
				'SUCCESS' => $success_messages,
				'WARNING' => $warning_messages,
				'HTML_BOX' => '<div class="messages">' . $messages_html . '</div>'
			],
			'TEMPLATE_DIR' => [
				'ROOT' => $controllerName != 'AdminController' ? Paths::SITE_TEMPLATE : Paths::SITE_ADMIN_TEMPLATE,
				'IMAGES' => $controllerName != 'AdminController' ? Paths::SITE_TEMPLATE_IMG : Paths::SITE_ADMIN_TEMPLATE_IMG
			],
			'SECURITY' => [
				'AUTH' => new Auth(),
				'TOKEN' => self::$_generateToken ? Token::generate($clientIp, $pageId, $clientBrowser) : '',
				'CAPTCHA' => self::$_captcha
			]
		];
	}

	private static function generateMessagesHtml(array $messages, string $type): string {
		if (empty($messages)) {
			return '';
		}
		$html = "<ul class=\"$type\">";
		foreach ($messages as $message) {
			$html .= "<li>$message</li>";
		}		
		return "$html</ul>";
	}
	
	public function setController(string $name) : void {
		$this->_controller = $name;
	}
	
	public function setMethod(string $name) : void {
		$this->_method = $name;
	}
	
	public function setParams(array $params) : void {
		$this->_urlParams = $params;
	}
	
	private function _getMethodType(string $name): MethodTypesEnum {
		if (str_ends_with($name, 'Action')) {
			return MethodTypesEnum::ACTION;
		} elseif (str_ends_with($name, 'FormView')) {
			return MethodTypesEnum::FORMVIEW;
		} elseif (str_ends_with($name, 'View')) {
			return MethodTypesEnum::VIEW;
		}
		throw new RequestException("La nomenclatura del método \"{$this->_method}\" no permite esclarecer su tipo.");
	}
	
	private function _methodPreExecution(MethodTypesEnum $methodType) : void {
		if(MethodTypesEnum::ACTION === $methodType) {
			self::_saveFormDataToSession();
			self::_cleanMessages();
		} else {
			self::_getFormDataFromSession();
			self::$_generateToken = true;
			self::$_captcha = Captcha::generate();
		}
	}

	private function _methodPostExecution(Controller $controller, MethodTypesEnum $methodType, string $controllerName): void {
		if ($methodType === MethodTypesEnum::VIEW || $methodType === MethodTypesEnum::FORMVIEW) {
			$templatePath = $controllerName !== 'AdminController' ? Paths::SITE_TEMPLATE : Paths::SITE_ADMIN_TEMPLATE;
			// Recuperar el contexto.
			$context = $controller->getContext();
			$context['PAGE']['GENERATION_TIME'] = microtime(true) - self::$_generationstartTime;
			define('CONTEXT', $context);
			// Cargar la vista.
			include_once $templatePath . $this->_controller . '-' . $this->_method . '.html';
			// Limpiar los mensajes.
			self::_cleanMessages();
		} else {
			Session::delete(Constants::SESSION_CAPTCHA);
			self::$_captcha = '';
			self::$_generateToken = false;
		}
	}
	
	private function _initVars(): void {
		// Controlador.
		$this->_controller = !empty($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'InitController';
		// Método.
		$this->_method = !empty($_GET['method']) ? $_GET['method'] : 'indexView';
		// Parámetros.
		foreach ($_GET as $key => $value) {
			if ($key !== 'controller' && $key !== 'method') {
				$this->_urlParams[$key] = $value;
			}
		}
	}
	
	private function _cleanMessages() : void {
		Session::put(Constants::SESSION_ERROR_MESSAGES, []);
		Session::put(Constants::SESSION_SUCCESS_MESSAGES, []);
		Session::put(Constants::SESSION_WARNING_MESSAGES, []);
	}
	
	private function _getFormDataFromSession() : void {
		if(Session::exists(Constants::SESSION_FORMDATA)) {
			$_POST = Session::get(Constants::SESSION_FORMDATA);
			Session::delete(Constants::SESSION_FORMDATA);
		}
	}
	
	private function _saveFormDataToSession() : void {
		if(isset($_POST)) {
			Session::put(Constants::SESSION_FORMDATA, $_POST);
		}
	}
	
	private static function _getLastVisitorPage(string $clientIp, string $clientBrowser) : void {
		VisitorModel::getLastVisit($clientIp, $clientBrowser);
	}
	
	private static function _saveVisitor(string $clientIp, string $clientBrowser, string $requestPage) : void {
		VisitorModel::insert($clientIp, $clientBrowser, $requestPage);
	}
}
