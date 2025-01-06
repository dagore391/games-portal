<?php
	namespace app\security;
	
	use app\Controller;
	use app\data\UserLevelsConstants;
	
	final class Security {
		public static function getFullControllerMethodsAccessPermissions(Controller $controller) : array {
			// Se recuperan los métodos contenidos en la clase Controller.
			$parentMethods = self::_getControllerMethods($controller);
			// Se agregan los permisos de todos los métodos que no se encuentran en el padre.
			$accessPermissions = [];
			foreach((new \ReflectionClass($controller))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				if(!in_array($method->getName(), $parentMethods)) {
					$controllerMethodPermissions = $controller->getMethodPermissions($method->getName());
					// Si no se ha definido ningún permiso para el método, se añade uno por defecto.
					$accessPermissions[$method->getName()] = sizeof($controllerMethodPermissions) > 0 ? $controllerMethodPermissions : array(UserLevelsConstants::ALL);
				}
			}
			// Se devuelven los permisos de todos los métodos accesibles.
			return $accessPermissions;
		}
		
		public static function checkAccess(Controller $controller, string $methodName, ?string $userGroup = null) : bool {
			$methodPermissions = $controller->getMethodPermissions($methodName);
			if($userGroup == null && in_array(UserLevelsConstants::NONE, $methodPermissions, true)) {
				return false;
			} else if($userGroup == null && in_array(UserLevelsConstants::REGISTER, $methodPermissions, true)) {
				return false;
			} else if($userGroup != null && in_array(UserLevelsConstants::GUEST, $methodPermissions, true)) {
				return false;
			} else if($userGroup != null && !in_array(UserLevelsConstants::ALL, $methodPermissions, true) && !in_array(UserLevelsConstants::REGISTER, $methodPermissions, true) && !in_array($userGroup, $methodPermissions, true)) {
				return false;
			} else if($userGroup == null && !in_array(UserLevelsConstants::GUEST, $methodPermissions, true) && !in_array(UserLevelsConstants::ALL, $methodPermissions, true)) {
				return false;
			}
			return true;
		}
		
		private static function _getControllerMethods(Controller $controller) : array {
			$controllerMethods = [];
			foreach((new \ReflectionClass(get_parent_class($controller)))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				array_push($controllerMethods, $method->getName());
			}
			return $controllerMethods;
		}
		
		public static function generateRandomString(int $length = 10) : string {
			$characters = '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}
		
		public static function ipStringToBinary(string $ipString) : string {
			return inet_pton($ipString);
		}
		
		public static function ipBinaryToString(string $ipBinary) : string {
			return inet_ntop($ipBinary);
		}
		
		public static function hashString(string $stringToHash) : string {
			return hash('SHA512', $stringToHash);
		}
	}
?>
