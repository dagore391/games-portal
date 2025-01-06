<?php
namespace app\security;

use app\config\Constants;
use app\data\UserLevelsConstants;
use app\model\MemberModel;

class Auth {
    private static $_errors = [];

    public static function find(string $username): ?object {
        return MemberModel::getByUsername($username);
    }

    public static function findEmail(string $email): ?object {
        return MemberModel::getByEmail($email);
    }

    public static function isAuthenticated(): bool {
        return self::_isActivated();
    }

    public static function logIn(string $username, string $password, string $ip): bool {
        $validation = true;
        self::$_errors = [];
        // Se busca el usuario en la base de datos.
        $member = self::find($username);
        // No se ha encontrado un usuario con el alias facilitado.
        if ($member === null) {
            array_push(self::$_errors, 'El usuario y/o contraseña no son válidos.');
            $validation = false;
        }
        $failedLogInAttempts = MemberModel::countFailedLogInAttempts($username, $ip);
        // Se comprueba si se ha sobrepasado el número de intentos de acceso permitidos.
        if ($failedLogInAttempts >= Constants::ATTEMPT_LIMIT) {
            self::$_errors[0] = 'Ha superado el nº de intentos permitidos.';
            return false;
        }
        // Tras pasar todas las validaciones, si el alias y la contraseña coinciden, se efectúa el login.
        if ($validation && Hash::make($password, $member->salt) === $member->password) {
            // La cuenta no está activa.
            if ($member->account_state != 'ACTIVED') {
                $validation = false;
                if (sizeof(self::$_errors) === 0) {
                    array_push(self::$_errors, 'Lo sentimos, pero la cuenta no está activa.');
                }
            } else {
                // Se actualiza la fecha del último inicio de sesión del usuario
                MemberModel::updateLastLogin($member->username);
                // Se crea la sesión.
                Session::put(Constants::SESSION_USERNAME, $member->username);
                // header("Set-Cookie: key=value; path=/; domain=localhost; HttpOnly; SameSite=Lax");
                // header('Set-Cookie: ' . Constants::SESSION_USERNAME . '=' . Session::get(Constants::SESSION_USERNAME) . '; Domain=localhost; Secure; HttpOnly');
            }
        } else {
            $validation = false;
            if (sizeof(self::$_errors) === 0) {
                array_push(self::$_errors, 'El usuario y/o contraseña no son válidos.');
            }
        }
        // Se registra el intento en el histórico sólo si la cuenta se encuentra temporalmente bloqueada.
        if ($failedLogInAttempts < Constants::ATTEMPT_LIMIT) {
            MemberModel::addAttemptToLoginHistory($ip, $username, $validation);
        }
        // Se devuelve el resultado de la operación.
        return $validation;
    }

    public static function getAuthenticatedUser(): ?object {
        if (Session::exists(Constants::SESSION_USERNAME)) {
            // Se actualiza la fecha del último inicio de sesión del usuario
            MemberModel::updateLastLogin(Session::get(Constants::SESSION_USERNAME));
            // Se recupera la información de base de datos.
            return self::find(Session::get(Constants::SESSION_USERNAME));
        }
        return null;
    }

    public static function logOut(): void {
        Session::delete(Constants::SESSION_USERNAME);
    }

    public static function errors(): array {
        return self::$_errors;
    }

    public static function getUsername(): string {
        return Session::exists(Constants::SESSION_USERNAME) ? Session::get(Constants::SESSION_USERNAME) : '';
    }

    public static function isAdmin(): bool {
        $member = null;
        if (self::isAuthenticated()) {
            // Se recupera la información de base de datos.
            $member = self::find(Session::get(Constants::SESSION_USERNAME));
        }
        return $member != null && $member->account_group === UserLevelsConstants::ADMIN;
    }

    public static function isManager(): bool {
        $member = null;
        if (self::isAuthenticated()) {
            // Se recupera la información de base de datos.
            $member = self::find(Session::get(Constants::SESSION_USERNAME));
        }
        return $member != null && $member->account_group === UserLevelsConstants::MANAGER;
    }
    
    public static function isModerator(): bool {
        $member = null;
        if (self::isAuthenticated()) {
            // Se recupera la información de base de datos.
            $member = self::find(Session::get(Constants::SESSION_USERNAME));
        }
        return $member != null && $member->account_group === UserLevelsConstants::MODERATOR;
    }

    public static function signUp(string $username, string $email, string $password, int $inboxSize, string $accountState, string $accountGroup): array {
        $errors = [];
        // Se busca el usuario en la base de datos.
        $member = self::find($username);
        // El alias ya está en uso por otro usuario registrado.
        if ($member !== null) {
            array_push($errors, 'Este nombre de usuario ya está en uso.');
            return $errors;
        }
        // El alias ya está en uso por otro usuario registrado.
        $member = self::findEmail($email);
        if ($member !== null) {
            array_push($errors, 'Esta dirección de email ya está en uso.');
            return $errors;
        }
        // Se genenara el salt.
        $salt = Security::generateRandomString(16);
        // Se genenara el salt.
        $activationCode = Security::generateRandomString(128);
        // Se cifra la contraseña.
        $passwordHash = Hash::make($password, $salt);
        // Se da de alta el usuario en la base de datos.
        $errors = MemberModel::insert($username, $passwordHash, $salt, $email, $inboxSize, $accountState, $accountGroup, [], $activationCode);
        if(sizeof($errors) > 0) {
            return $errors;
        }
        // TODO: Dar de alta el código de activación en BBDD con tiempo de caducidad.
        // Se envía un mail para la activación de la cuenta.
        Email::sendMail($email, "Activa tu cuenta introduciendo el siguiente código: {$activationCode}", 'Necesita activar su cuenta.');
        // La operación finalizó correctamente.
        return $errors;
    }

    private static function _isActivated(): bool {
        $userAccount = self::getAuthenticatedUser();
        return $userAccount != null && $userAccount->account_state === 'ACTIVED';
    }
}
