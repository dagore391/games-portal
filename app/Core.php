<?php
namespace app;

use app\config\Constants;
use app\config\Paths;
use app\exception\RequestException;
use app\exception\SecurityException;
use app\security\Auth;
use app\security\Logger;

final class Core {
	private static $_request = null;
	private static $_logInUser = null;
	
	public static function init() : void {
		try {
			// Se cargan todas las dependencias necesarios para arrancar la aplicación.
			self::_importDependencies();
			try {
				// Se cargan las configuraciones de la aplicación.
				self::_loadConfiguration();
				// Se recuperan los datos del usuario autenticado.
				self::$_logInUser = Auth::getAuthenticatedUser();
				// Se importan los ficheros de idiomas.
				self::_importLanguageFiles();
				// Se procesa la solicitud del cliente.
				self::$_request = new Request(self::$_logInUser);
				// Se invoca el método que se ajuste a la petición del cliente.
				self::$_request->callControllerMethod();
			} catch(RequestException $exception) {
				Logger::logTrace($exception->getMessage(), $exception);
				self::_callErrorControllerMethod('eView', 404);
			} catch(SecurityException $exception) {
				Logger::logTrace($exception->getMessage(), $exception);
				self::_callErrorControllerMethod('eView', 403);
			} catch(\Exception $exception) {
				Logger::logError($exception->getMessage(), $exception);
				self::_callErrorControllerMethod('eView', 500);
			}
		} catch(\Exception $exception) {
			Logger::logError($exception->getMessage(), $exception);
		}
	}
	
	private static function _importDependencies() : void {
		// Se importan los ficheros de configuración.
		include_once 'config/Constants.php';
		include_once 'config/Paths.php';
		include_once Paths::CONFIG . 'Urls.php';
		// BBDD, Enumeraciones, Constantes y otras variables.
		include_once Paths::DATA . 'AccountGroupsConstants.php';
		include_once Paths::DATA . 'AccountStatesConstants.php';
		include_once Paths::DATA . 'Database.php';
		include_once Paths::DATA . 'Dates.php';
		include_once Paths::DATA . 'EntryCategoriesConstants.php';
		include_once Paths::DATA . 'InfoTypesConstants.php';
		include_once Paths::DATA . 'Input.php';
		include_once Paths::DATA . 'MethodTypesEnum.php';
		include_once Paths::DATA . 'Pagination.php';
		include_once Paths::DATA . 'PrivacyTypesConstants.php';
		include_once Paths::DATA . 'ReportTypesConstants.php';
		include_once Paths::DATA . 'Sanitize.php';
		include_once Paths::DATA . 'UrlsEnum.php';
		include_once Paths::DATA . 'UserLevelsConstants.php';
		include_once Paths::DATA . 'Validate.php';
		// Manejo de ficheros.
		include_once Paths::FILES . 'SecureImport.php';
		include_once Paths::FILES . 'TemplateHtml.php';
		include_once Paths::FILES . 'UploadFile.php';
		// Models.
		include_once Paths::MODEL . 'CheatModel.php';
		include_once Paths::MODEL . 'CompanyModel.php';
		include_once Paths::MODEL . 'GameModel.php';
		include_once Paths::MODEL . 'EntryModel.php';
		include_once Paths::MODEL . 'EntryCommentModel.php';
		include_once Paths::MODEL . 'ForumModel.php';
		include_once Paths::MODEL . 'ForumPostModel.php';
		include_once Paths::MODEL . 'InputDataModel.php';
		include_once Paths::MODEL . 'LanguageModel.php';
		include_once Paths::MODEL . 'MemberModel.php';
		include_once Paths::MODEL . 'MetadataModel.php';
		include_once Paths::MODEL . 'MetagroupModel.php';
		include_once Paths::MODEL . 'PageModel.php';
		include_once Paths::MODEL . 'PlatformModel.php';
		include_once Paths::MODEL . 'SecurityTokenModel.php';
		include_once Paths::MODEL . 'VisitorModel.php';
		// Librerías básicas.
		include_once 'Controller.php';
		include_once 'Request.php';
		// Excepciones personalizadas.
		include_once Paths::EXCEPTION . 'RequestException.php';
		include_once Paths::EXCEPTION . 'SecurityException.php';
		// Seguridad.
		include_once Paths::SECURITY . 'Captcha.php';
		include_once Paths::SECURITY . 'Cookie.php';
		include_once Paths::SECURITY . 'Email.php';
		include_once Paths::SECURITY . 'Hash.php';
		include_once Paths::SECURITY . 'Logger.php';
		include_once Paths::SECURITY . 'Token.php';
		include_once Paths::SECURITY . 'Session.php';
		include_once Paths::SECURITY . 'Auth.php';
		include_once Paths::SECURITY . 'Security.php';
	}

	private static function _loadConfiguration() : void {
		// Configuración de errores.
		ini_set('display_errors', Constants::DISPLAY_ERRORS);
		ini_set('log_errors', Constants::LOG_ERRORS);
		ini_set('error_reporting', Constants::ERROR_REPORTING);
		ini_set('error_log', Logger::LOG_FILE_PATH);
		// Se configuran las preferencias para el envío de correo electrónico.
		ini_set('smtp', Constants::SMTP);
		ini_set('smtp_port', Constants::SMTP_PORT);
	}
	
	private static function _importLanguageFiles() : void {
		$langTag = Constants::SITE_LANG;
		if(Auth::isAuthenticated() && self::$_logInUser != null && file_exists(Paths::LANG . self::$_logInUser->language . '.php')) {
			$langTag = self::$_logInUser->language;
		}
		include_once Paths::LANG . $langTag . '.php';
	}
	
	private static function _callErrorControllerMethod(string $methodName, int $code) : void {
		self::$_request->setController('ErrorController');
		self::$_request->setMethod($methodName);
		self::$_request->setParams([$code]);
		self::$_request->callControllerMethod();
	}
}
