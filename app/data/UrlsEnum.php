<?php
namespace app\data;

enum UrlsEnum : string {
	// Admin Controller.
	case ADMIN_ACTION_ADDCHEAT = '/admincp/cheats/action/new.html';
	case ADMIN_ACTION_ADDCOMPANY = '/admincp/companies/action/new.html';
	case ADMIN_ACTION_ADDENTRY = '/admincp/entries/action/new.html';
	case ADMIN_ACTION_ADDGAME = '/admincp/games/action/new.html';
	case ADMIN_ACTION_ADDMEMBER = '/admincp/users/action/new.html';
	case ADMIN_ACTION_ADDMETADATA = '/admincp/metadatas/action/new.html';
	case ADMIN_ACTION_ADDMETAGROUP = '/admincp/metagroups/action/new.html';
	case ADMIN_ACTION_ADDPLATFORM = '/admincp/platforms/action/new.html';
	case ADMIN_ACTION_EDITCHEAT = '/admincp/cheats/cheat-%s/action/edit.html';
	case ADMIN_ACTION_EDITCOMPANY = '/admincp/companies/company-%s/action/edit.html';
	case ADMIN_ACTION_EDITENTRY = '/admincp/entries/entry-%s/action/edit.html';
	case ADMIN_ACTION_EDITGAME = '/admincp/games/game-%s/action/edit.html';
	case ADMIN_ACTION_EDITMEMBER = '/admincp/users/user-%s/action/edit.html';
	case ADMIN_ACTION_EDITMETADATA = '/admincp/metadatas/metadata-%s/action/edit.html';
	case ADMIN_ACTION_EDITMETAGROUP = '/admincp/metagroups/metagroup-%s/action/edit.html';
	case ADMIN_ACTION_EDITPLATFORM = '/admincp/platforms/platform-%s/action/edit.html';
	case ADMIN_VIEW_ADDCHEAT = '/admincp/cheats/new.html';
	case ADMIN_VIEW_ADDCOMPANY = '/admincp/companies/new.html';
	case ADMIN_VIEW_ADDENTRY = '/admincp/entries/new.html';
	case ADMIN_VIEW_ADDGAME = '/admincp/games/new.html';
	case ADMIN_VIEW_ADDMEMBER = '/admincp/users/new.html';
	case ADMIN_VIEW_ADDMETADATA = '/admincp/metadatas/new.html';
	case ADMIN_VIEW_ADDMETAGROUP = '/admincp/metagroups/new.html';
	case ADMIN_VIEW_ADDPLATFORM = '/admincp/platforms/new.html';
	case ADMIN_VIEW_EDITCHEAT = '/admincp/cheats/cheat-%s/edit.html';
	case ADMIN_VIEW_EDITCOMPANY = '/admincp/companies/company-%s/edit.html';
	case ADMIN_VIEW_EDITENTRY = '/admincp/entries/entry-%s/edit.html';
	case ADMIN_VIEW_EDITGAME = '/admincp/games/game-%s/edit.html';
	case ADMIN_VIEW_EDITMEMBER = '/admincp/users/user-%s/edit.html';
	case ADMIN_VIEW_EDITMETADATA = '/admincp/metadatas/metadata-%s/edit.html';
	case ADMIN_VIEW_EDITMETAGROUP = '/admincp/metagroups/metagroup-%s/edit.html';
	case ADMIN_VIEW_EDITPLATFORM = '/admincp/platforms/platform-%s/edit.html';
	case ADMIN_VIEW_LISTCHEAT = '/admincp/cheats/list/page-%s.html';
	case ADMIN_VIEW_LISTCOMPANY = '/admincp/companies/list/page-%s.html';
	case ADMIN_VIEW_LISTENTRY = '/admincp/entries/list/page-%s.html';
	case ADMIN_VIEW_LISTGAME = '/admincp/games/list/page-%s.html';
	case ADMIN_VIEW_LISTMEMBER = '/admincp/users/list/page-%s.html';
	case ADMIN_VIEW_LISTMETADATA = '/admincp/metadatas/list/page-%s.html';
	case ADMIN_VIEW_LISTMETAGROUP = '/admincp/metagroups/list/page-%s.html';
	case ADMIN_VIEW_LISTPLATFORM = '/admincp/platforms/list/page-%s.html';
	case ADMIN_VIEW_INDEX = '/admincp/dashboard.html';
	// Company Controller.
	case COMPANY_VIEW_LIST = '/companies/list/page-%s.html';
	case COMPANY_VIEW_SHOW = '/companies/show/company-%s.html';
	// Entry Controller.
	case ENTRY_LIST_VIEW = '/entries/%s/list/page-%s.html';
	case ENTRY_VIEW_SHOW = '/entries/%s/show/entry-%s/page-%s.html';
	case ENTRY_VIEW_NEWCOMMENT = '/entries/%s/entry-%s/action/new-comment.html';
	// Error Controller.
	case ERROR_VIEW_403 = '/error-403.html';
	case ERROR_VIEW_404 = '/error-404.html';
	// Forum Controller.
	case FORUM_VIEW_INDEX = '/forums.html';
	case FORUM_ACTION_NEWPOST = '/forums/forum-%s/topic-%s/action/new-post.html';
	case FORUM_ACTION_NEWTOPIC = '/forums/forum-%s/topic/action/new.html';
	case FORUM_VIEW_NEWTOPIC = '/forums/forum-%s/topic/new.html';
	case FORUM_VIEW_SHOW = '/forums/forum-%s/page-%s.html';
	case FORUM_VIEW_SHOWTOPIC = '/forums/forum-%s/topics/show/topic-%s/page-%s.html';
	// Game Controller.
	case GAME_VIEW_LIST = '/games/list/%s/%s/page-%s.html';
	case GAME_VIEW_SHOW = '/games/%s/show/game-%s-%s.html';
	case GAME_VIEW_TOP_MEDIAS = '/games/top-medias.html';
	case GAME_VIEW_TOP_USERS = '/games/top-users.html';
	case GAME_VIEW_TOP_WEB = '/games/top-web.html';
	case GAME_ACTION_ADDTOLIST = '/games/%s/game-%s/action/add-to-%s.html';
	case GAME_ACTION_REMOVEFROMLIST = '/games/%s/game-%s/action/remove-from-%s.html';
	case GAME_ACTION_RATE = '/games/%s/game-%s/action/rate.html';
	case GAME_ACTION_REMOVERATE = '/games/%s/game-%s/action/unrate.html';
	// Init Controller.
	case INIT_VIEW_INDEX = '/';
	case INIT_VIEW_CUSTOM_PAGE = '/page-%s.html';
	case INIT_VIEW_SEARCH = '/search.html';
	// Member Controller.
	case MEMBER_ACTION_ACCEPT_COOKIES = '/accept-cookies.html';
	case MEMBER_ACTION_ACTIVATE_ACCOUNT = '/user/%s/action/activate-account.html';
	case MEMBER_VIEW_ACTIVATE_ACCOUNT = '/user/%s/activate-account.html';
	case MEMBER_VIEW_FRIENDS = '/user/friends.html';
	case MEMBER_ACTION_LOCK = '/user/user-%s/action/lock.html';
	case MEMBER_ACTION_LOGIN = '/user/action/login.html';
	case MEMBER_VIEW_LOGIN = '/login.html';
	case MEMBER_ACTION_LOGOUT = '/user/action/logout.html';
	case MEMBER_VIEW_INBOXPM = '/user/pm/inbox.html';
	case MEMBER_VIEW_SHOWPM = '/user/pm/show/message-%s.html';
	case MEMBER_ACTION_PROCESSPM = '/user/pm/action/process.html';
	case MEMBER_ACTION_PROCESSREQUEST = '/user/requests/action/process.html';
	case MEMBER_VIEW_PROFILE = '/user/profile-%s.html';
	case MEMBER_ACTION_REMOVEFRIEND = '/user/friends/action/remove.html';
	case MEMBER_VIEW_REQUESTS = '/user/requests.html';
	case MEMBER_ACTION_SENDREQUEST = '/user/requests/action/send-to-%s.html';
	case MEMBER_VIEW_SETTINGS = '/user/settings.html';
	case MEMBER_VIEW_SIGNUP = '/signup.html';
	case MEMBER_ACTION_SIGNUP = '/user/action/signup.html';
	case MEMBER_ACTION_UNLOCK = '/user/user-%s/action/unlock.html';
	case MEMBER_ACTION_UPDATESETTINGS = '/user/settings/action/update/%s.html';
	case MEMBER_ACTION_REPORTMESSAGE = '/user/action/report/%s/message-%s.html';
	// Platform Controller.
	case PLATFORM_VIEW_LIST = '/platforms/list/page-%s.html';
}
