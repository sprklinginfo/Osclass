<?php
/**
 * OSClass – software for creating and publishing online classified advertising platforms
 *
 * Copyright (C) 2010 OSCLASS
 *
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class CAdminEmails extends AdminSecBaseModel
{
    //specific for this class
    private $emailManager ;
    
    function __construct() {
        parent::__construct() ;

        //specific things for this class
        $this->emailManager = Page::newInstance() ;
    }

    //Business Layer...
    function doModel() {
        parent::doModel() ;

        //specific things for this class
        switch ($this->action)
        {

            case 'edit':
                if(Params::getParam("id")=='') {
                    $this->redirectTo(osc_admin_base_url(true)."?page=emails");
                }
                $this->_exportVariableToView("email", $this->emailManager->findByPrimaryKey(Params::getParam("id")));
                $this->doView("emails/frm.php");
                break;
            case 'edit_post':
                $id = Params::getParam("id");
                $s_internal_name = Params::getParam("s_internal_name");
                
                $aFieldsDescription = array();
                $postParams = Params::getParamsAsArray();
                foreach ($postParams as $k => $v) {
                    if(preg_match('|(.+?)#(.+)|', $k, $m)) {
                        $aFieldsDescription[$m[1]][$m[2]] = $v;
                    }
                }
                
                foreach($aFieldsDescription as $k => $_data) {
                    $this->emailManager->updateDescription($id, $k, $_data['s_title'], $_data['s_text']);
                }
                
                if(!$this->emailManager->internalNameExists($id, $s_internal_name)) {
                    if(!$this->emailManager->isIndelible($id)) {
                        $this->emailManager->updateInternalName($id, $s_internal_name);
                    }
                    osc_add_flash_message( _m('The email/alert has been updated'), 'admin' );
                    $this->redirectTo(osc_admin_base_url(true)."?page=emails");
                }
                osc_add_flash_message( _m('You can\'t repeat internal name'), 'admin');
                $this->redirectTo(osc_admin_base_url(true)."?page=emails?action=edit&id=" . $id);
                break;
            default:

                if(Session::_get("adminLocale")=='') {
                    $this->_exportVariableToView("prefLocale", osc_language());
                } else {
                    $this->_exportVariableToView("prefLocale", Session::_get("adminLocale"));
                }
                $this->_exportVariableToView("emails", $this->emailManager->listAll(1));
                $this->doView("emails/index.php");

        }
    }

    //hopefully generic...
    function doView($file) {
        osc_current_admin_theme_path($file) ;
    }
}

?>
