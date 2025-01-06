<?php
namespace app\controller;

use app\config\Constants;
use app\config\Paths;
use app\config\Urls;
use app\data\AccountGroupsConstants;
use app\data\AccountStatesConstants;
use app\data\Input;
use app\data\PrivacyTypesConstants;
use app\data\ReportTypesConstants;
use app\data\UrlsEnum;
use app\data\UserLevelsConstants;
use app\data\Validate;
use app\file\UploadFile;
use app\model\ForumModel;
use app\model\GameModel;
use app\model\LanguageModel;
use app\model\MemberModel;
use app\security\Auth;
use app\security\Hash;
use app\security\Security;
use app\security\Session;
use app\model\EntryCommentModel;
use app\model\ForumPostModel;
use app\security\Cookie;

class MemberController extends \app\Controller {
    protected $_methodAccessPermissions = [
        'acceptCookiesAction' => [UserLevelsConstants::ALL],
        'activateAccountAction' => [UserLevelsConstants::GUEST],
        'activateAccountView' => [UserLevelsConstants::GUEST],
        'friendsView' => [UserLevelsConstants::REGISTER],
        'lockAction' => [UserLevelsConstants::REGISTER],
        'logInView' => [UserLevelsConstants::GUEST],
        'logInAction' => [UserLevelsConstants::GUEST],
        'logOutAction' => [UserLevelsConstants::REGISTER],
        'pmInboxView' => [UserLevelsConstants::REGISTER],
        'pmReadView' => [UserLevelsConstants::REGISTER],
        'processPmAction' => [UserLevelsConstants::REGISTER],
        'processRequestAction' => [UserLevelsConstants::REGISTER],
        'profileView' => [UserLevelsConstants::ALL],
        'removeFriendAction' => [UserLevelsConstants::REGISTER],
        'reportMessageAction' => [UserLevelsConstants::REGISTER],
        'requestsView' => [UserLevelsConstants::REGISTER],
        'sendRequestAction' => [UserLevelsConstants::REGISTER],
        'settingsView' => [UserLevelsConstants::REGISTER],
        'signUpView' => [UserLevelsConstants::GUEST],
        'unlockAction' => [UserLevelsConstants::REGISTER],
        'updateSettingsAction' => [UserLevelsConstants::REGISTER]
    ];

    private $_privacyTypes = [
        PrivacyTypesConstants::ALL => LANG_ALL,
        PrivacyTypesConstants::FRIENDS => LANG_ONLY_FRIENDS,
        PrivacyTypesConstants::NONE => LANG_NOBODY
    ];

    private $_privacyValues = [
        PrivacyTypesConstants::ALL,
        PrivacyTypesConstants::FRIENDS,
        PrivacyTypesConstants::NONE
    ];
    
    public function acceptCookiesAction(): void {
        Cookie::set(Constants::COOKIE_ACCEPT, Security::generateRandomString(128), Constants::COOKIE_EXPIRATION_TIME_IN_DAYS);
        // Se redirige al perfil del usuario que se ha intentado bloquear.
        Urls::redirectTo(UrlsEnum::INIT_VIEW_INDEX);
    }
    
    public function activateAccountAction(string $username): void {
        // Se recupera la cuenta de usuario de la base de datos.
        $member = MemberModel::getByUsername($username);
        // Si no existe o ya está activa, se redirige a la página de error 404.
        if($member === null || $member->account_state !== AccountStatesConstants::DEACTIVATED) {
            Urls::redirectTo(UrlsEnum::MEMBER_VIEW_LOGIN);
        }
        // Se comprueba que se haya recibido la información del formulario.
        if (Input::exists('post')) {
            $validation = new Validate($this->_context['CLIENT']['IP'], 'MemberController-ActivateAccountView', $this->_context['CLIENT']['BROWSER']);
            $validation->check($_POST, ['code' => ['required' => true, 'min' => 1]], true, true);
            // Si no pasa las validaciones, se informa al usuario.
            if (!$validation->passed()) {
                Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
            } elseif($member->activation_code !== Input::getPost('code')) {
                Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'El código de activación no es válido.');
                Urls::redirectTo(UrlsEnum::MEMBER_VIEW_ACTIVATE_ACCOUNT, [$username]);
            } elseif(sizeof(MemberModel::activateAccount($username)) === 0) {
                Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'La cuenta ha sido activada correctamente.');
                // Se redirige a la página principal.
                Urls::redirectTo(UrlsEnum::MEMBER_VIEW_LOGIN);
                exit();
            }
        }
        // Si no se han enviado datos mediante POST o se han producido errores durante la activación de la cuenta, se redirige al usuario al formulario de activación.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_ACTIVATE_ACCOUNT, [$username]);
    }
    
    public function activateAccountView(string $username): void {
        // Se recupera la cuenta de usuario de la base de datos.
        $member = MemberModel::getByUsername($username);
        // Si no existe o ya está activa, se redirige a la página de error 404.
        if($member === null || $member->account_state !== AccountStatesConstants::DEACTIVATED) {
            Urls::redirectTo(UrlsEnum::MEMBER_VIEW_LOGIN);
        }
    }

    public function friendsView(): void {
        $this->_context['FRIENDS'] = MemberModel::getFriends($this->_context['CLIENT']['LOGIN_USER']->id);
    }

    public function lockAction(int $id): void {
        if (Auth::isAuthenticated()) {
            MemberModel::lock($this->_context['CLIENT']['LOGIN_USER']->id, $id);
        }
        // Se redirige al perfil del usuario que se ha intentado bloquear.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_PROFILE, [ $id ]);
    }

    public function logInAction(): void {
        if (Input::exists('post')) {
            $validation = new Validate($this->_context['CLIENT']['IP'], 'MemberController-LogInView', $this->_context['CLIENT']['BROWSER']);
            $validation->check($_POST, [
                'username' => ['required' => true, 'max' => 20, 'min' => 4],
                'password' => ['required' => true]
            ], true, true);
            // Se verifica que el usuario se ha podido autenticar. En caso afirmativo, se le redirige a la página de inicio.
            if (!$validation->passed()) {
                Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
            } else if (! Auth::logIn(Input::getPost('username'), Input::getPost('password'), $this->_context['CLIENT']['IP'])) {
                Session::put(Constants::SESSION_ERROR_MESSAGES, Auth::errors());
            } else {
                Urls::redirectTo(UrlsEnum::INIT_VIEW_INDEX);
                exit();
            }
        }
        // Si no se han enviado datos mediante POST o se han producido errores durante la autenticación, se redirige al usuario al formulario de logIn.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_LOGIN);
    }

    public function logInView(): void {
        // Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
        $this->_context['FORMDATA'] = [
            'USERNAME' => Input::getPostWithDefaultValue('username', '')
        ];
    }

    public function logOutAction(): void {
        Auth::logOut();
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_LOGIN);
    }

    public function pmInboxView(): void {
        $this->_context['INBOX_MESSAGES'] = MemberModel::getInboxMessages($this->_context['CLIENT']['LOGIN_USER']->id);
    }

    public function pmReadView(int $id): void {
        // Se recupera la información del mensaje a consultar si pertenece al usuario autenticado.
        $this->_context['PRIVATE_MESSAGE'] = MemberModel::getMessage($id, $this->_context['CLIENT']['LOGIN_USER']->id);
        // Si no se ha encontrado ningún mensaje, se redirige a la página de error.
        Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], is_null($this->_context['PRIVATE_MESSAGE']));
        // Se marca el mensaje como leído, si no se encuentra marcado como tal.
        if ($this->_context['PRIVATE_MESSAGE']->is_read == 0) {
            MemberModel::markMessageAsRead($id, $this->_context['CLIENT']['LOGIN_USER']->id);
        }
    }

    public function processPmAction(): void {
        if (Input::exists('post')) {
            if (is_array(Input::getPost('messages')) && sizeof(Input::getPost('messages')) > 0) {
                $validation = new Validate($this->_context['CLIENT']['IP'], 'MemberController-pmInboxView', $this->_context['CLIENT']['BROWSER']);
                $validation->check($_POST, [], true, false);
                if ($validation->passed()) {
                    $deletedMessages = 0;
                    foreach (Input::getPost('messages') as $message) {
                        if (is_numeric($message) && MemberModel::deleteMessage($message, $this->_context['CLIENT']['LOGIN_USER']->id) != 0) {
                            $deletedMessages ++;
                        }
                    }
                    if ($deletedMessages === 0) {
                        Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'No ha sido posible eliminar ningún mensaje.');
                    } else if (sizeof(Input::getPost('messages')) != $deletedMessages) {
                        Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'Únicamente ha sido posible eliminar ' . $deletedMessages . ' de ' . sizeof(Input::getPost('messages')) . ' mensajes.');
                    } else {
                        Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'Los mensajes seleccionados se han eliminado correctamente.');
                    }
                } else {
                    Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
                }
            } else {
                Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'No se ha seleccionado ningún mensaje.');
            }
        }
        // Se redirige a la vista de gestión de mensajes privados.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_INBOXPM);
    }

    public function processRequestAction(): void {
        if (Input::exists('post')) {
            if (is_array(Input::getPost('requests')) && Input::getPost('requests') > 0) {
                $validation = new Validate($this->_context['CLIENT']['IP'], 'MemberController-requestsView', $this->_context['CLIENT']['BROWSER']);
                $validation->check($_POST, [
                    'action' => ['label' => 'Acción', 'required' => true, 'values' => ['accept', 'reject']]
                ], false);
                if ($validation->passed()) {
                    $processedRequests = 0;
                    foreach (Input::getPost('requests') as $request) {
                        if (is_numeric($request) && Input::getPost('action') === 'accept' && MemberModel::updateRequest($request, $this->_context['CLIENT']['LOGIN_USER']->id, 'FRIENDSHIP') != 0) {
                            $processedRequests ++;
                        } else if (is_numeric($request) && Input::getPost('action') === 'reject' && MemberModel::deleteRequest($this->_context['CLIENT']['LOGIN_USER']->id, $request, 'REQUEST') != 0) {
                            $processedRequests ++;
                        }
                    }
                    if ($processedRequests === 0) {
                        Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'No ha sido posible procesar ninguna solicitud.');
                    } else if (sizeof(Input::getPost('requests')) != $processedRequests) {
                        Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'Únicamente ha sido posible prcesar ' . $processedRequests . ' de ' . sizeof(Input::getPost('requests')) . ' solicitudes.');
                    } else {
                        Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'Las solicitudes seleccionadas se han procesado correctamente.');
                    }
                } else {
                    Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
                }
            } else {
                Session::put(Constants::SESSION_ERROR_MESSAGES, 'No se ha seleccionado ninguna solicitud.');
            }
        }
        // Se redirige a la vista de gestión de solicitudes.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_REQUESTS);
    }

    public function profileView(int $id): void {
        $this->_context['MEMBER'] = $this->_context['CLIENT']['LOGIN_USER'];
        // Si el perfil no es del usuario logado, se recupera la información del nuevo usuario y se verifica su privacidad.
        if (is_null($this->_context['MEMBER']) || $this->_context['MEMBER']->id != $id) {
            // Se recuperan los datos del perfil del usuario.
            $member = MemberModel::getById($id);
            // Se verifica que el id se corresponda con el de algún usuario.
            Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], is_null($member));
            // Si el perfil del usuario es privado o visible para amigos y el usuario que lo visualiza no está en su lista de amigos, se bloquea el acceso.
            if ($member->myprofile_visivility === 'NONE' || $member->myprofile_visivility === 'FRIENDS' && ! MemberModel::areFriends($id, $this->_context['LOGIN_USER']->id)) {
                Urls::redirectTo(UrlsEnum::ERROR_VIEW_404);
            }
            // Se sobreescriben los datos del usuario autenticado por los del usuario consultado.
            $this->_context['MEMBER'] = $member;
        }
        // Se recuperan los datos del perfil del usuario consultado.
        $this->_context['TOTAL_FRIENDS'] = MemberModel::getTotalFriends($id);
        $this->_context['TOTAL_MESSAGES'] = ForumModel::getTotalPostsByMember($id);
        $this->_context['TOTAL_GAMESINCOLLECTION'] = GameModel::selectCountMemberGameList($id, 'COLLECTION');
        $this->_context['TOTAL_GAMESINWISHLIST'] = GameModel::selectCountMemberGameList($id, 'WISHLIST');
        $this->_context['LATEST_POSTS'] = ForumModel::getLimitLatestPostsByMember($id, 5);
    }

    public function removeFriendAction(): void {
        if (Input::exists('post')) {
            if (is_array(Input::getPost('requests')) && sizeof(Input::getPost('requests')) > 0) {
                $validation = new Validate($this->_context['CLIENT']['IP'], 'MemberController-friendsView', $this->_context['CLIENT']['BROWSER']);
                $validation->check($_POST, [], true, false);
                if ($validation->passed()) {
                    $deletedRequests = 0;
                    foreach (Input::getPost('requests') as $request) {
                        if (is_numeric($request) && MemberModel::deleteRequest($request, $this->_context['CLIENT']['LOGIN_USER']->id, 'FRIENDSHIP') != 0) {
                            $deletedRequests ++;
                        }
                    }
                    if ($deletedRequests === 0) {
                        Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'No ha sido posible eliminar ningún elemento.');
                    } else if (sizeof(Input::getPost('requests')) != $deletedRequests) {
                        Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'Únicamente ha sido posible eliminar ' . $deletedRequests . ' de ' . sizeof(Input::getPost('requests')) . ' elementos.');
                    } else {
                        Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'Los elementos seleccionados se han eliminado correctamente.');
                    }
                } else {
                    Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
                }
            } else {
                Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'No se ha seleccionado ningún elemento.');
            }
        }
        // Se redirige a la vista de amigos.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_FRIENDS, []);
    }
    
    public function reportMessageAction(string $type, int $messageId): void {
        $messageType = strtoupper($type);
        // Se verifica que el usuario esté autenticado y el tipo de reporte sea válido.
        if (!Auth::isAuthenticated()) {
            Urls::redirectTo(UrlsEnum::ERROR_VIEW_403, []);
        } else if(($messageType != ReportTypesConstants::TYPE_COMMENT && $messageType != ReportTypesConstants::TYPE_POST)) {
            Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
        }
        $errors = [];
        // Se comprueba que exista el post o comentario reportado.
        switch($messageType) {
            case ReportTypesConstants::TYPE_COMMENT :
                // Se recupera el comentario de la base de datos.
                $commentObj = EntryCommentModel::getById($messageId);
                // Si no existe el comentario, se redirige a la página de error 404.
                if ($commentObj === null) {
                    Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
                }
                // Se reporta el mensaje.
                self::reportMessage($commentObj->comment_author, $messageId, $commentObj->comment_content, $messageType);
                // Se redirige a la entrada donde se encontraba el comentario reportado.
                Urls::redirectTo(UrlsEnum::ENTRY_VIEW_SHOW, [strtolower($commentObj->entry_category), $commentObj->comment_entry, 1]);
            case ReportTypesConstants::TYPE_POST :
                // Se recupera el post de la base de datos.
                $forumPostObj = ForumPostModel::getById($messageId);
                // Si no existe el comentario, se redirige a la página de error 404.
                if ($forumPostObj === null) {
                    Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
                }
                // Se guarda el topic para la redirección.
                $topic = $forumPostObj->post_topic !== null ? $forumPostObj->post_topic : $forumPostObj->post_id;
                // Se reporta el mensaje.
                self::reportMessage($forumPostObj->member_id, $messageId, $forumPostObj->post_content, $messageType);
                // Se redirige al tema donde se encontraba el post reportado.
                Urls::redirectTo(UrlsEnum::FORUM_VIEW_SHOWTOPIC, [$forumPostObj->forum_id, $topic, 1]);
                break;
            default:
                // Si el caso no está contemplado, se redirige a la página de error 404.
                Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
                break;
        }
    }

    private function reportMessage(int $memberId, int $messageId, string $messageContent, string $messageType) : void {
        // Antes de reportar el mensaje, se comprueba que no se haya hecho antes por el mismo usuario.
        if(!MemberModel::checkReportedMessage($this->_context['CLIENT']['LOGIN_USER']->id, $memberId, $messageId, $messageType)) {
            $errors = MemberModel::reportMessage($this->_context['CLIENT']['LOGIN_USER']->id, $memberId, $messageId, $messageContent, $messageType);
        } else {
            $errors = ['Ya has reportado este mensaje.'];
        }
        // Se preparan los mensajes para notificar al usuario del resultado de la operación.
        if(sizeof($errors) > 0) {
            Session::put(Constants::SESSION_ERROR_MESSAGES, $errors);
        } else {
            Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'El mensaje se ha reportado correctamente.');
        }
    }

    public function requestsView(): void {
        $this->_context['FRIENDS_REQUESTS'] = MemberModel::getByMemberAndState($this->_context['CLIENT']['LOGIN_USER']->id, 'REQUEST');
    }

    public function sendRequestAction(int $id): void {
        if (Auth::isAuthenticated()) {
            MemberModel::sendRequest($this->_context['CLIENT']['LOGIN_USER']->id, $id);
        }
        // Se redirige al perfil del usuario al que se ha intentado enviar una solicitud.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_PROFILE, [$id]);
    }

    public function settingsView(): void {
        $this->_context['LANGUAGES'] = LanguageModel::selectAll();
        $this->_context['PRIVACY_TYPES'] = $this->_privacyTypes;
    }

    public function signUpAction(): void {
        if (Input::exists('post')) {
            // Se validan los campos del formulario.
            $validation = new Validate($this->_context['CLIENT']['IP'], 'MemberController-signUpView', $this->_context['CLIENT']['BROWSER']);
            $validation->check($_POST, [
                'username' => ['label' => LANG_USERNAME, 'required' => true, 'pattern' => 'username'],
                'email' => ['label' => LANG_EMAIL, 'required' => true, 'max' => 100, 'min' => 4],
                'password1' => ['label' => LANG_NEW_PASSWORD, 'required' => true, 'max' => 24, 'min' => 8, 'matches' => 'password2', 'pattern' => 'password'],
                'password2' => ['label' => LANG_REPEAT_NEW_PASSWORD, 'required' => true, 'max' => 24, 'min' => 8, 'matches' => 'password1']
            ], true, false);
            if (! $validation->passed()) {
                Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
            } else {
                // Se da de alta el registro en la base de datos
                $signup = Auth::signUp(Input::getPost('username'), Input::getPost('email'), Input::getPost('password1'), Constants::DEFAULT_INBOX_SIZE, AccountStatesConstants::DEACTIVATED, AccountGroupsConstants::USER);
                if (sizeof($signup) === 0) {
                    Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf('Su cuenta se ha creado correctamente. Se ha enviado un email a la siguiente dirección "%s" con el código de activación.', Input::getPost('email')));
                    // Redirección.
                    Urls::redirectTo(UrlsEnum::MEMBER_VIEW_ACTIVATE_ACCOUNT, array(Input::getPost('username')));
                } else {
                    Session::put(Constants::SESSION_ERROR_MESSAGES, $signup);
                }
            }
        }
        // Redirección.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_SIGNUP);
    }

    public function signUpView(): void {
        // Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
        $this->_context['FORMDATA'] = [
            'USERNAME' => Input::getPostWithDefaultValue('username', ''),
            'EMAIL' => Input::getPostWithDefaultValue('email', '')
        ];
    }

    public function unlockAction(int $id): void {
        if (Auth::isAuthenticated()) {
            MemberModel::unlock($this->_context['CLIENT']['LOGIN_USER']->id, $id);
        }
        // Se redirige al perfil del usuario que se ha intentado desbloquear.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_PROFILE, [$id]);
    }

    public function updateSettingsAction(string $action): void {
        $member = $this->_context['CLIENT']['LOGIN_USER'];
        // Se comprueba si se está procesando alguna acción, para procesar el formulario correspondiente.
        if (Input::exists('post') && ! empty($member)) {
            $validation = new Validate($this->_context['CLIENT']['IP'], 'MemberController-settingsView', $this->_context['CLIENT']['BROWSER']);
            switch ($action) {
                case 'uavatar':
                    $errors = [];
                    if (!empty($_FILES['avatar'])) {
                        $errors = UploadFile::upload($_FILES['avatar'], Paths::UMEMBERAVATAR, $member->username, array(
                            'image/jpeg'
                        ), 262144, true);
                    } else {
                        Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'No se ha subido ninguna imagen para su avatar.');
                    }
                    // Se actualizan los mensajes de error para notificarlos al cliente.
                    if (sizeof($errors) > 0) {
                        Session::put(Constants::SESSION_ERROR_MESSAGES, $errors);
                    } else {
                        Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'Tu avatar se ha actualizado correctamente.');
                    }
                    break;
                case 'upassword':
                    // Se validan los campos para el cambio de contraseña del usuario.
                    $validation->check($_POST, [
                        'cpassword' => ['label' => LANG_CURRENT_PASSWORD, 'required' => true],
                        'password1' => ['label' => LANG_NEW_PASSWORD, 'required' => true, 'max' => 24, 'min' => 8, 'matches' => 'password2', 'pattern' => 'password'],
                        'password2' => ['label' => LANG_REPEAT_NEW_PASSWORD, 'required' => true, 'max' => 24, 'min' => 8, 'matches' => 'password1']
                    ], true, false);
                    // Se verifica que los campos hayan superado las validaciones.
                    if ($validation->passed()) {
                        // Se comprueba que la antigua contraseña es correcta para poder autorizar el cambio.
                        if ($member->password === Hash::make(Input::getPost('cpassword'), $member->salt)) {
                            // Se cifra la nueva contraseña antes de insertarla en base de datos.
                            $newPassword = Hash::make(Input::getPost('password1'), $member->salt);
                            // Se actualiza la contraseña del usuario.
                            if (! MemberModel::updatePassword($member->id, $newPassword)) {
                                Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'Se ha producido un error al intentar actualizar su contraseña.');
                            } else {
                                Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'La contraseña se ha cambiado correctamente.');
                            }
                        } else {
                            Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'El valor del campo "' . LANG_CURRENT_PASSWORD . '" no se corresponde con el de su contraseña.');
                        }
                    } else {
                        Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
                    }
                    break;
                case 'upreferences':
                    $languageTags = [];
                    $languages = LanguageModel::selectAll();
                    foreach ($languages as $row) {
                        array_push($languageTags, $row->tag);
                    }
                    // Se validan los campos para el cambio de contraseña del usuario.
                    $validation->check($_POST, [
                        'password' => ['label' => LANG_YOUR_PASSWORD, 'required' => true, 'max' => 24, 'min' => 2],
                        'language' => ['label' => LANG_SITE_LANGUAGE, 'required' => true, 'values' => $languageTags],
                        'profile' => ['label' => LANG_WHO_CAN_VISIT_MY_PROFILE, 'required' => true, 'values' => $this->_privacyValues],
                        'messages' => ['label' => LANG_WHO_CAN_PUBLISH_IN_MY_PROFILE, 'required' => true, 'values' => $this->_privacyValues],
                        'pm' => ['label' => LANG_WHO_CAN_SEND_ME_PM, 'required' => true, 'values' => $this->_privacyValues]
                    ], false);
                    // Se verifica que los campos hayan superado las validaciones.
                    if ($validation->passed()) {
                        // Se comprueba que la antigua contraseña es correcta para poder autorizar el cambio.
                        if ($member->password === Hash::make(Input::getPost('password'), $member->salt)) {
                            // Se actualizan las preferencias del usuario.
                            if (! MemberModel::updatePreferences($member->id, $member->email, Input::getPost('profile'), Input::getPost('messages'), Input::getPost('pm'), Input::getPost('language'))) {
                                Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'Se ha producido un error al intentar actualizar sus preferencias.');
                            } else {
                                Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'Sus preferencias se han actualizado correctamente.');
                            }
                        } else {
                            Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'El valor del campo "' . LANG_CURRENT_PASSWORD . '" no se corresponde con el de su contraseña.');
                        }
                    } else {
                        Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
                    }
                    break;
                case 'uemail':
                    // Se validan los campos para el cambio de contraseña del usuario.
                    $validation->check($_POST, [
                        'email' => ['label' => LANG_EMAIL, 'required' => true, 'max' => 100, 'min' => 4]
                    ], false);
                    // Se verifica que los campos hayan superado las validaciones.
                    if ($validation->passed()) {
                        // Se comprueba que la antigua contraseña es correcta para poder autorizar el cambio.
                        if ($member->password != Hash::make(Input::getPost('password'), $member->salt)) {
                            Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'El valor del campo "' . LANG_YOUR_PASSWORD . '" no se corresponde con el de su contraseña.');
                            // Se actualizan las preferencias del usuario.
                        } else if ($member->email == Input::getPost('email')) {
                            Session::addElement(Constants::SESSION_ERROR_MESSAGES, 'El valor del campo "' . LANG_EMAIL . '" debe ser distinto al actual.');
                        } else {
                            // TODO: Enviar el e-mail.
                            Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'Se ha enviado un código a la nueva dirección para poder actualizar su e-mail.');
                        }
                    } else {
                        Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
                    }
                    break;
            }
        }
        // Se redirige a la página de ajustes del usuario.
        Urls::redirectTo(UrlsEnum::MEMBER_VIEW_SETTINGS);
    }
}
