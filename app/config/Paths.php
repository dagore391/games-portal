<?php
	namespace app\config;

	final class Paths {
		public const APP = 'app' . DIRECTORY_SEPARATOR;
		public const CONFIG = self::APP . 'config' . DIRECTORY_SEPARATOR;
		public const CONTROLLER = self::APP . 'controller' . DIRECTORY_SEPARATOR;
		public const DATA = self::APP . 'data' . DIRECTORY_SEPARATOR;
		public const EXCEPTION = self::APP . 'exception' . DIRECTORY_SEPARATOR;
		public const FILES = self::APP . 'file' . DIRECTORY_SEPARATOR;
		public const LANG = self::APP . 'lang' . DIRECTORY_SEPARATOR;
		public const MODEL = self::APP . 'model' . DIRECTORY_SEPARATOR;
		public const SECURITY = self::APP . 'security' . DIRECTORY_SEPARATOR;
		public const TEMPLATE = self::APP . 'template' . DIRECTORY_SEPARATOR;
		public const SITE_ADMIN_TEMPLATE = self::TEMPLATE . Constants::SITE_ADMIN_TEMPLATE . DIRECTORY_SEPARATOR;
		public const SITE_ADMIN_TEMPLATE_IMG = self::SITE_ADMIN_TEMPLATE . Constants::SITE_ADMIN_TEMPLATE . DIRECTORY_SEPARATOR;
		public const SITE_TEMPLATE = self::TEMPLATE . Constants::SITE_TEMPLATE . DIRECTORY_SEPARATOR;
		public const SITE_TEMPLATE_IMG = self::SITE_TEMPLATE . 'images' . DIRECTORY_SEPARATOR;
		public const UPLOADS = self::APP . 'uploads' . DIRECTORY_SEPARATOR;
		public const UENTRY = self::UPLOADS . 'entry' . DIRECTORY_SEPARATOR;
		public const UENTRYFEATURED = self::UENTRY . 'featured' . DIRECTORY_SEPARATOR;
		public const UENTRYTHUMBNAIL = self::UENTRY . 'thumbnail' . DIRECTORY_SEPARATOR;
		public const UGAME = self::UPLOADS . 'game' . DIRECTORY_SEPARATOR;
		public const UGAMECOVER = self::UGAME . 'cover' . DIRECTORY_SEPARATOR;
		public const USCREENSHOT = self::UGAME . 'screenshots' . DIRECTORY_SEPARATOR;
		public const UMETADATA = self::UPLOADS . 'metadata' . DIRECTORY_SEPARATOR;
		public const UMEMBER = self::UPLOADS . 'member' . DIRECTORY_SEPARATOR;
		public const UMEMBERAVATAR = self::UMEMBER . 'avatar' . DIRECTORY_SEPARATOR;
	}
?>
