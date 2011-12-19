<?
/**
* @package carl_util
* @subpackage dir_service
* @author Mark Heiman
* 
* A general purpose directory service interface
*/

/**
* Luther LDAP Directory Service -- Interface for access to LDAP directories
* @subpackage dir_service
* @author Mark Heiman
*/

include_once('paths.php');
//include_once(CARL_UTIL_INC . '/dir_service/services/ds_ldap.php');
include_once('/usr/local/webapps/reason/reason_package_local/carl_util/dir_service/services/ds_ldap_luther.php');

class ds_ldap_luther_directory extends ds_ldap_luther {

	/**
	* array Connection settings for this service. 
	* @access private
	* @var array
	*/
	var $_conn_params = array(
//	  	'host' => 'replica-1.luther.edu',
	  	'host' => 'ldap.luther.edu',
        	'port' => 389,
		'ldap_version' => 3,
		'use_tls' => false,
		'lookup_dn' => 'cn=webauth,dc=luther,dc=edu',
        'lookup_password' => 'daewoo$friendly$$cow',
		);
	
	/**
	* array Settings for the current search 
	* @access private
	* @var array
	*/
	var $_search_params = array(
		'base_attrs' => array('uid','mail','edupersonprimaryaffiliation','edupersonaffiliation','ds_username'),
		'subtree_search' => false,
		'base_dn' => 'ou=People,dc=luther,dc=edu',
		// To add Alumni use the line below //
		//'base_dn' => 'dc=luther,dc=edu',
		);
		
	/**
	* array Dependencies for generic attributes 
	* @access private
	* @var array
	*/
	var $_gen_attr_depend = array(
		'ds_username' => array('uid'),
		'ds_email' => array('mail'),
		'ds_firstname' => array('givenname'),
		'ds_lastname' => array('sn'),
		'ds_fullname' => array('displayname','cn'),
		//Steve changes
		/*  'ds_phone' => array('telephonenumber'),  this returns home phone*/
		'ds_title' => array('title'),
		'ds_phone' => array('officephone'),
		'ds_affiliation' => array('edupersonaffiliation','edupersonprimaryaffiliation'),
		'ds_groupname' => array('cn'),
		'ds_member' => array('memberuid'),
		'ds_owner' => array('creatorsname'),
		);
	/**
	* Validate username and password 
	* @access public
	* @param string $username Userid
	* @param string $password Password
	*/
	function authenticate($username, $password) {
		$this->_conn_params = array(
//	  	'host' => 'replica-1.luther.edu',
	  	'host' => 'ldap.luther.edu',
        	'port' => 389,
		'ldap_version' => 3,
		'use_tls' => false,
		'lookup_dn' => 'uid='.$username.',ou=People,dc=luther,dc=edu',
        	'lookup_password' => $password,
		);
                $this->authenticate_helper();
	}
        function authenticate_helper() {
		if( !ldap_bind( $this->_conn, $this->_conn_params['lookup_dn'], $this->_conn_params['lookup_password'] ) ) {
			$this->_error = sprintf( 'LDAP bind failed for %s, %s' , $this->_conn_params['lookup_dn'], ldap_error( $this->_conn ));
		}
		return $bind_result;
	}
}
?>