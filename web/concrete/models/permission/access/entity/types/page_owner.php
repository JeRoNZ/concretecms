<?
defined('C5_EXECUTE') or die("Access Denied.");

class PageOwnerPermissionAccessEntity extends PermissionAccessEntity {

	public function getAccessEntityUsers(PermissionAccess $pae) {
		if ($pae instanceof PagePermissionAccess) {
			$c = $pae->getPermissionObject();
		} else if ($pae instanceof AreaPermissionAccess) {
			$c = $pae->getPermissionObject()->getAreaCollectionObject();
		} else if ($pae instanceof BlockPermissionAccess) {
			$a = $pae->getPermissionObject()->getBlockAreaObject();
			$c = $a->getAreaCollectionObject();
		}
		if (is_object($c)) {
			$ui = UserInfo::getByID($c->getCollectionUserID());
			$users = array($ui);
			return $users;
		}
	}

	public function validate(PermissionAccess $pae) {
		$users = $this->getAccessEntityUsers($pae);
		if (count($users) == 0) {
			return false;
		} else if (is_object($users[0])) {
			$u = new User();
			$users[0]->getUserID() == $u->getUserID();
		}
	}
	
	public function getAccessEntityTypeLinkHTML() {
		$html = '<a href="javascript:void(0)" onclick="ccm_choosePermissionAccessEntityPageOwner()">' . t('Page Owner') . '</a>';
		return $html;		
	}

	public static function getAccessEntitiesForUser($user) {
		$entities = array();
		$db = Loader::db();
		if ($user->isRegistered()) { 
			$pae = PageOwnerPermissionAccessEntity::getOrCreate();
			$r = $db->GetOne('select cID from Pages where uID = ?', array($user->getUserID()));
			if ($r > 0) {
				$entities[] = $pae;
			}
		}
		return $entities;		
	}
	
	public static function getOrCreate() {
		$db = Loader::db();
		$petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = \'page_owner\'');
		$peID = $db->GetOne('select peID from PermissionAccessEntities where petID = ?', 
			array($petID));
		if (!$peID) { 
			$db->Execute("insert into PermissionAccessEntities (petID) values(?)", array($petID));
			$peID = $db->Insert_ID();
		}
		return PermissionAccessEntity::getByID($peID);
	}

	public function load() {
		$db = Loader::db();
		$this->label = t('Page Owner');
	}

}