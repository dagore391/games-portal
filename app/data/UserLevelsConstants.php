<?php
namespace app\data;

final class UserLevelsConstants {
	// Niveles generales.
	public const ALL = 'ALL';
	public const NONE = 'NONE';
	public const GUEST = 'GUEST';
	public const REGISTER = 'REGISTER';
	// Niveles de específicos.
	public const USER = 'USER';
	public const WRITER = 'WRITER';
	public const MODERATOR = 'MODERATOR';
	public const MANAGER = 'MANAGER';
	public const ADMIN = 'ADMIN';
}
