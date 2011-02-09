<?php

if ( ! defined( 'IN_TBDEV_ADMIN' ) ){
        require("include/bittorrent.php");
        $lang = array_merge(load_language('ad_index'));
        print "<h1>{$lang['text_incorrect']}</h1>{$lang['text_cannot']}";
        exit();
}

require_once "include/user_functions.php";

    $lang = array_merge( $lang, load_language('ad_themes') );

        $HTML="";

        if(isset($_GET['act'])){
                $ACT=$_GET['act'];
                if(!is_valid_id($ACT))stderr("{$lang['themes_error']}", "{$lang['themes_inv_act']}");
                if($ACT==1){//--EDIT
                        if(!isset($_GET['id']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        $ID=$_GET['id'];
                        if(!is_valid_id($ID))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        $TEMPLATE=mysql_query("SELECT * FROM stylesheets WHERE id=".sqlesc($ID)." LIMIT 1");
                        $TEM=mysql_fetch_array($TEMPLATE);
                        $HTML.="
                     <div class='cblock'>
                         <div class='cblock-header'>{$lang['themes_edit_tem']} $TEM[name]</div>
                         <div class='cblock-content'>
                             <form action='{$TBDEV['baseurl']}/admin.php?action=themes&amp;act=4' method='post'>
                                  <input type='hidden' value='{$TEM['id']}' name='ori' />
                                  <table width='50%'>
                                        <tr><td colspan='2' class='colhead' align='center'></td></tr>
                                        <tr><td class='rowhead'>{$lang['themes_id']}<br/>{$lang['themes_explain_id']}</td><td><input type='text' value='{$TEM['id']}' name='id' /></td></tr>
                                        <tr><td class='rowhead'>{$lang['themes_name']}</td><td><input type='text' value='{$TEM['name']}' name='title' /></td></tr>
                                        <tr>
                                           <td class='rowhead'>{$lang['themes_is_folder']}</td>
                                           <td><b>".(file_exists(ROOT_PATH."/templates/{$TEM['uri']}/template.php")?"{$lang['themes_file_exists']}":"{$lang['themes_not_exists']}")."</b></td>
                                        </tr>
                                        <tr><td class='colhead' colspan='2' align='center'><input type='submit' value='{$lang['themes_save']}' /></td></tr>
                                  </table>
                             </form>
                         </div>
                     </div>";
                }
                if($ACT==2){//--DELETE
                        if(!isset($_GET['id']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        $ID=$_GET['id'];
                        if(!is_valid_id($ID))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        stderr("{$lang['themes_delete_q']}", "{$lang['themes_delete_sure_q']}<a href='{$TBDEV['baseurl']}/admin.php?action=themes&amp;act=5&amp;id=$ID&amp;sure=1'>
                        {$lang['themes_delete_sure_q2']}</a> {$lang['themes_delete_sure_q3']}");
                }
                if($ACT==3){//--ADD NEW
                        $IDS=mysql_query("SELECT id FROM stylesheets");
                        while($ID=mysql_fetch_array($IDS)){
                                if(file_exists(ROOT_PATH."/templates/{$ID['uril']}/template.php"))$TAKEN[]="<font color='green'>$ID[id]</font>";
                                else $TAKEN[]="<font color='red'>$ID[id]</font>";
                        }
                        $HTML.="
                     <div class='cblock'>
                         <div class='cblock-header'>{$lang['themes_addnew']}</div>
                         <div class='cblock-content'>
                             <form action='admin.php?action=themes&amp;act=6' method='post'>
                                  <table width='50%'>
                                        <tr valign='middle'>
                                           <td class='rowhead'>{$lang['themes_id']}</td>
                                           <td><input type='text' value='' name='id' /><br />{$lang['themes_takenids']}<b>".implode(", ", $TAKEN)."</b></td>
                                        </tr>
                                        <tr valign='middle'>
                                           <td class='rowhead'>{$lang['themes_name']}</td>
                                           <td><input type='text' value='' name='name' /></td>
                                        </tr>
                                        <tr><td colspan='2'>{$lang['themes_guide']}</td></tr>
                                        <tr><td class='colhead' colspan='2' align='center'><input type='submit' value='{$lang['themes_add']}' /></td></tr>
                                  </table>
                             </form>
                         </div>
                     </div>";
                }
                if($ACT==4){//--SEVE EDIT
                        if(!isset($_POST['id']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        if(!isset($_POST['ori']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        if(!isset($_POST['title']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_name']}");
                        $ID=$_POST['id'];
                        $ORI=$_POST['ori'];
                        $NAME=$_POST['title'];
                        if(!is_valid_id($ID))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        if(!is_valid_id($ORI))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        $CURRENT=mysql_query("SELECT * FROM stylesheets WHERE id=".sqlesc($ORI));
                        $CUR=mysql_fetch_array($CURRENT);
                        if($ID!=$CUR['id'])$EDIT[]="id=".sqlesc($ID);
                        if($NAME!=$CUR['name'])$EDIT[]="name=".sqlesc($NAME);
                        if(!mysql_query("UPDATE stylesheets SET ".implode(", ", $EDIT)." WHERE id=".sqlesc($ORI)))stderr("{$lang['themes_error']}", "{$lang['themes_some_wrong']}");
                        header("Location: {$TBDEV['baseurl']}/admin.php?action=themes&msg=1");
                }
                if($ACT==5){//--DELETE FINAL
                        if(!isset($_GET['id']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        $ID=$_GET['id'];
                        if(!is_valid_id($ID))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        if(!isset($_POST['sure']))header("Location: admin.php?action=themes");
                        if($_POST['sure']!="1")header("Location: admin.php?action=themes");
                        mysql_query("DELETE FROM stylesheets WHERE id=".sqlesc($ID));
                        $RANDSTYLE=mysql_fetch_array(mysql_query("SELECT id FROM stylesheets ORDER BY RAND() LIMIT 1"));
                        mysql_query("UPDATE users SET stylesheet=".sqlesc($RANDSTYLE['id'])." WHERE stylesheet=".sqlesc($ID));
                        header("Location: {$TBDEV['baseurl']}/admin.php?action=themes&msg=2");
                }
                if($ACT==6){//--ADD NEW SAVE
                        if(!isset($_POST['id']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        if(!isset($_POST['name']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_name']}");
                        if(!file_exists(ROOT_PATH."/templates/".intval($_POST['id'])."/template.php"))stderr("{$lang['themes_nofile']}",
                        "{$lang['themes_inv_file']}<a href='{$TBDEV['baseurl']}/admin.php?action=themes&amp;act=7&amp;amp;id=".intval($_POST['id'])."&amp;name={$_POST['name']}'>{$lang['themes_file_exists']}</a>/
                        <a href='{$TBDEV['baseurl']}/admin.php?action=themes'>{$lang['themes_not_exists']}</a>");
                        @mysql_query("INSERT INTO stylesheets(id, uri, name)VALUES(".sqlesc($_POST['id']).", '', ".sqlesc($_POST['name']).")");
                        header("Location: {$TBDEV['baseurl']}/admin.php?action=themes&msg=3");
                }
                if($ACT==7){//--ADD NEW IF FOLDER NO EXISTS
                        if(!isset($_GET['id']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_id']}");
                        if(!isset($_GET['name']))stderr("{$lang['themes_error']}", "{$lang['themes_inv_name']}");
                        $ID=$_GET['id'];
                        $NAME=$_GET['name'];
                        @mysql_query("INSERT INTO stylesheets(id, uri, name)VALUES(".sqlesc($ID).", '', ".sqlesc($NAME).")");
                        header("Location: admin.php?action=themes&msg=3");
                }
        }

        if(isset($_GET['msg'])){
                $MSG=$_GET['msg'];
                if($MSG>0)$HTML.="<h1>{$lang['themes_msg']}</h1>";
        }

        if(!isset($_GET['act'])){

                $HTML .= "
                     <div class='cblock'>
                         <div class='cblock-header'>Template Manager<div style='float:right;'><a href='{$TBDEV['baseurl']}/admin.php?action=themes&amp;act=3'><span class='btn'>{$lang['themes_addnew']}</span></div></div>
                         <div class='cblock-content'>
                             <table width='100%'>
                                   <tr><td colspan='4'></a></td></tr>
                                   <tr>
                                      <td class='colhead'>{$lang['themes_id']}</td>
                                      <td class='colhead'>{$lang['themes_name']}</td>
                                      <td class='colhead'>{$lang['themes_is_folder']}</td>
                                      <td class='colhead'>{$lang['themes_e_d']}</td>
                                   </tr>";

                $TEMPLATES=mysql_query("SELECT * FROM stylesheets");
                while($TE=mysql_fetch_array($TEMPLATES)){
                        $HTML.="
                                   <tr>
                                      <td class='rowhead'>$TE[id]</td>
                                      <td>".htmlsafechars($TE['name'])."</td>
                                      <td><b>".(file_exists(ROOT_PATH."/templates/{$TE['uri']}/template.php")?"{$lang['themes_file_exists']}":"{$lang['themes_not_exists']}")."</b></td>
                                      <td>
                                         <a href='{$TBDEV['baseurl']}/admin.php?action=themes&amp;act=1&amp;id=$TE[id]'>[{$lang['themes_edit']}]</a>
                                         <a href='{$TBDEV['baseurl']}/admin.php?action=themes&amp;act=2&amp;id=$TE[id]'>[{$lang['themes_delete']}]</a>
                                      </td>
                                   </tr>";
                }

                $HTML.="           <tr><td class='colhead' colspan='4' align='center'>{$lang['themes_credits']}</td></tr>
                             </table>
                         </div>
                     </div>";
        }

    print stdhead("{$lang['stdhead_templates']}") . $HTML . stdfoot();

?>
