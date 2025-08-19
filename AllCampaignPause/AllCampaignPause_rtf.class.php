<?php

class AllCampaignPause_rtf
{
   var $Db;
   var $Erro;
   var $Ini;
   var $Lookup;
   var $nm_data;
   var $Texto_tag;
   var $Arquivo;
   var $Tit_doc;
   var $sc_proc_grid; 
   var $NM_cmp_hidden = array();

   //---- 
   function __construct()
   {
      $this->nm_data   = new nm_data("en_us");
      $this->Texto_tag = "";
   }


function actionBar_isValidState($buttonName, $buttonState)
{
    return false;
}

   //---- 
   function monta_rtf()
   {
      $this->inicializa_vars();
      $this->gera_texto_tag();
      $this->grava_arquivo_rtf();
      if ($this->Ini->sc_export_ajax)
      {
          $this->Arr_result['file_export']  = NM_charset_to_utf8($this->Rtf_f);
          $this->Arr_result['title_export'] = NM_charset_to_utf8($this->Tit_doc);
          $Temp = ob_get_clean();
          if ($Temp !== false && trim($Temp) != "")
          {
              $this->Arr_result['htmOutput'] = NM_charset_to_utf8($Temp);
          }
          $oJson = new Services_JSON();
          echo $oJson->encode($this->Arr_result);
          exit;
      }
      else
      {
          $this->progress_bar_end();
      }
   }

   //----- 
   function inicializa_vars()
   {
      global $nm_lang;
      if (isset($GLOBALS['nmgp_parms']) && !empty($GLOBALS['nmgp_parms'])) 
      { 
          $GLOBALS['nmgp_parms'] = str_replace("@aspass@", "'", $GLOBALS['nmgp_parms']);
          $todox = str_replace("?#?@?@?", "?#?@ ?@?", $GLOBALS["nmgp_parms"]);
          $todo  = explode("?@?", $todox);
          foreach ($todo as $param)
          {
               $cadapar = explode("?#?", $param);
               if (1 < sizeof($cadapar))
               {
                   if (substr($cadapar[0], 0, 11) == "SC_glo_par_")
                   {
                       $cadapar[0] = substr($cadapar[0], 11);
                       $cadapar[1] = $_SESSION[$cadapar[1]];
                   }
                   if (isset($GLOBALS['sc_conv_var'][$cadapar[0]]))
                   {
                       $cadapar[0] = $GLOBALS['sc_conv_var'][$cadapar[0]];
                   }
                   elseif (isset($GLOBALS['sc_conv_var'][strtolower($cadapar[0])]))
                   {
                       $cadapar[0] = $GLOBALS['sc_conv_var'][strtolower($cadapar[0])];
                   }
                   nm_limpa_str_AllCampaignPause($cadapar[1]);
                   nm_protect_num_AllCampaignPause($cadapar[0], $cadapar[1]);
                   if ($cadapar[1] == "@ ") {$cadapar[1] = trim($cadapar[1]); }
                   $Tmp_par   = $cadapar[0];
                   $$Tmp_par = $cadapar[1];
                   if ($Tmp_par == "nmgp_opcao")
                   {
                       $_SESSION['sc_session'][$script_case_init]['AllCampaignPause']['opcao'] = $cadapar[1];
                   }
               }
          }
      }
      if (isset($total_chked)) 
      {
          $_SESSION['total_chked'] = $total_chked;
          nm_limpa_str_AllCampaignPause($_SESSION["total_chked"]);
      }
      if (isset($i)) 
      {
          $_SESSION['i'] = $i;
          nm_limpa_str_AllCampaignPause($_SESSION["i"]);
      }
      if (isset($arr_vl)) 
      {
          $_SESSION['arr_vl'] = $arr_vl;
          nm_limpa_str_AllCampaignPause($_SESSION["arr_vl"]);
      }
      if (isset($tot)) 
      {
          $_SESSION['tot'] = $tot;
          nm_limpa_str_AllCampaignPause($_SESSION["tot"]);
      }
      $dir_raiz          = strrpos($_SERVER['PHP_SELF'],"/") ;  
      $dir_raiz          = substr($_SERVER['PHP_SELF'], 0, $dir_raiz + 1) ;  
      $this->nm_location = $this->Ini->sc_protocolo . $this->Ini->server . $dir_raiz; 
      require_once($this->Ini->path_aplicacao . "AllCampaignPause_total.class.php"); 
      $this->Tot      = new AllCampaignPause_total($this->Ini->sc_page);
      $this->prep_modulos("Tot");
      $Gb_geral = "quebra_geral_" . $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['SC_Ind_Groupby'];
      if (method_exists($this->Tot,$Gb_geral))
      {
          $this->Tot->$Gb_geral();
          $this->count_ger = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['tot_geral'][1];
      }
      if (!$this->Ini->sc_export_ajax) {
          require_once($this->Ini->path_lib_php . "/sc_progress_bar.php");
          $this->pb = new scProgressBar();
          $this->pb->setRoot($this->Ini->root);
          $this->pb->setDir($_SESSION['scriptcase']['AllCampaignPause']['glo_nm_path_imag_temp'] . "/");
          $this->pb->setProgressbarMd5($_GET['pbmd5']);
          $this->pb->initialize();
          $this->pb->setReturnUrl("./");
          $this->pb->setReturnOption('volta_grid');
          $this->pb->setTotalSteps($this->count_ger);
      }
      $this->Arquivo    = "sc_rtf";
      $this->Arquivo   .= "_" . date("YmdHis") . "_" . rand(0, 1000);
      $this->Arquivo   .= "_AllCampaignPause";
      $this->Arquivo   .= ".rtf";
      $this->Tit_doc    = "AllCampaignPause.rtf";
   }
   //---- 
   function prep_modulos($modulo)
   {
      $this->$modulo->Ini    = $this->Ini;
      $this->$modulo->Db     = $this->Db;
      $this->$modulo->Erro   = $this->Erro;
      $this->$modulo->Lookup = $this->Lookup;
   }


   //----- 
   function gera_texto_tag()
   {
     global $nm_lang;
      global $nm_nada, $nm_lang;

      $_SESSION['scriptcase']['sc_sql_ult_conexao'] = ''; 
      $this->sc_proc_grid = false; 
      $nm_raiz_img  = ""; 
      if (isset($_SESSION['scriptcase']['sc_apl_conf']['AllCampaignPause']['field_display']) && !empty($_SESSION['scriptcase']['sc_apl_conf']['AllCampaignPause']['field_display']))
      {
          foreach ($_SESSION['scriptcase']['sc_apl_conf']['AllCampaignPause']['field_display'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['usr_cmp_sel']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['usr_cmp_sel']))
      {
          foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['usr_cmp_sel'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['php_cmp_sel']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['php_cmp_sel']))
      {
          foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['php_cmp_sel'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      $this->sc_where_orig   = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['where_orig'];
      $this->sc_where_atual  = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['where_pesq'];
      $this->sc_where_filtro = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['where_pesq_filtro'];
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['campos_busca']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['campos_busca']))
      { 
          $Busca_temp = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['campos_busca'];
          if ($_SESSION['scriptcase']['charset'] != "UTF-8")
          {
              $Busca_temp = NM_conv_charset($Busca_temp, $_SESSION['scriptcase']['charset'], "UTF-8");
          }
          $this->campaignname = (isset($Busca_temp['campaignname'])) ? $Busca_temp['campaignname'] : ""; 
          $tmp_pos = (is_string($this->campaignname)) ? strpos($this->campaignname, "##@@") : false;
          if ($tmp_pos !== false && !is_array($this->campaignname))
          {
              $this->campaignname = substr($this->campaignname, 0, $tmp_pos);
          }
          $this->status = (isset($Busca_temp['status'])) ? $Busca_temp['status'] : ""; 
          $tmp_pos = (is_string($this->status)) ? strpos($this->status, "##@@") : false;
          if ($tmp_pos !== false && !is_array($this->status))
          {
              $this->status = substr($this->status, 0, $tmp_pos);
          }
          $this->emailcount = (isset($Busca_temp['emailcount'])) ? $Busca_temp['emailcount'] : ""; 
          $tmp_pos = (is_string($this->emailcount)) ? strpos($this->emailcount, "##@@") : false;
          if ($tmp_pos !== false && !is_array($this->emailcount))
          {
              $this->emailcount = substr($this->emailcount, 0, $tmp_pos);
          }
      } 
      $this->nm_where_dinamico = "";
      $_SESSION['scriptcase']['AllCampaignPause']['contr_erro'] = 'on';
if (!isset($_SESSION['i'])) {$_SESSION['i'] = "";}
if (!isset($this->sc_temp_i)) {$this->sc_temp_i = (isset($_SESSION['i'])) ? $_SESSION['i'] : "";}
if (!isset($_SESSION['total_chked'])) {$_SESSION['total_chked'] = "";}
if (!isset($this->sc_temp_total_chked)) {$this->sc_temp_total_chked = (isset($_SESSION['total_chked'])) ? $_SESSION['total_chked'] : "";}
 $this->sc_temp_i = 0;
$this->sc_temp_total_chked = array();
if (isset($this->sc_temp_total_chked)) {$_SESSION['total_chked'] = $this->sc_temp_total_chked;}
if (isset($this->sc_temp_i)) {$_SESSION['i'] = $this->sc_temp_i;}
$_SESSION['scriptcase']['AllCampaignPause']['contr_erro'] = 'off'; 
      if  (!empty($this->nm_where_dinamico)) 
      {   
          $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['where_pesq'] .= $this->nm_where_dinamico;
      }   
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_name']))
      {
          $Pos = strrpos($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_name'], ".");
          if ($Pos === false) {
              $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_name'] .= ".rtf";
          }
          $this->Arquivo = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_name'];
          $this->Tit_doc = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_name'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_name']);
      }
      $this->arr_export = array('label' => array(), 'lines' => array());
      $this->arr_span   = array();

      $this->Texto_tag .= "<table>\r\n";
      $this->Texto_tag .= "<tr>\r\n";
      foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['field_order'] as $Cada_col)
      { 
          $SC_Label = (isset($this->New_label['campaignname'])) ? $this->New_label['campaignname'] : "Campaign Name"; 
          if ($Cada_col == "campaignname" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['startdate'])) ? $this->New_label['startdate'] : "Start Date"; 
          if ($Cada_col == "startdate" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['emailcount'])) ? $this->New_label['emailcount'] : "Total"; 
          if ($Cada_col == "emailcount" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['emailnosend'])) ? $this->New_label['emailnosend'] : "Sent To"; 
          if ($Cada_col == "emailnosend" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['sentper'])) ? $this->New_label['sentper'] : "Sent %"; 
          if ($Cada_col == "sentper" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['bounceemailno'])) ? $this->New_label['bounceemailno'] : "Bounce"; 
          if ($Cada_col == "bounceemailno" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['bounce'])) ? $this->New_label['bounce'] : "Total Bounce %"; 
          if ($Cada_col == "bounce" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['boun'])) ? $this->New_label['boun'] : "Bounce %"; 
          if ($Cada_col == "boun" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['noofopen'])) ? $this->New_label['noofopen'] : "Opened"; 
          if ($Cada_col == "noofopen" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['open'])) ? $this->New_label['open'] : "Opened %"; 
          if ($Cada_col == "open" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['totalopen'])) ? $this->New_label['totalopen'] : "Total Opened %"; 
          if ($Cada_col == "totalopen" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['noofresponse'])) ? $this->New_label['noofresponse'] : "Respond"; 
          if ($Cada_col == "noofresponse" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['responce'])) ? $this->New_label['responce'] : "Respond %"; 
          if ($Cada_col == "responce" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['totalresponce'])) ? $this->New_label['totalresponce'] : "Total Respond %"; 
          if ($Cada_col == "totalresponce" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['status'])) ? $this->New_label['status'] : "Status"; 
          if ($Cada_col == "status" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['recid'])) ? $this->New_label['recid'] : "Rec ID"; 
          if ($Cada_col == "recid" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['discription'])) ? $this->New_label['discription'] : "Discription"; 
          if ($Cada_col == "discription" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['mode'])) ? $this->New_label['mode'] : "Mode"; 
          if ($Cada_col == "mode" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['smtpprofile'])) ? $this->New_label['smtpprofile'] : "Smtp Profile"; 
          if ($Cada_col == "smtpprofile" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              $SC_Label = NM_charset_to_utf8($SC_Label);
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
      } 
      $this->Texto_tag .= "</tr>\r\n";
      $this->nm_field_dinamico = array();
      $this->nm_order_dinamico = array();
      $nmgp_select_count = "SELECT count(*) AS countTest from (SELECT     RecID,     CampaignName,     Discription,     Status,     Mode,     SmtpProfile,     SmtpServer,     UserName,     Password,     ApiProfile,     SendTo,     ToContact,     MsgType,     Subject,     Message,     FromMail,     FromName,     RecurringDuration,     CreatedBy,     CreatedDate,     ModifiedBy,     ModifiedTime,     ToEmail,     Template,     UploadFile,     EmailSend,     EmailCount,     StartDate,     StartTime,     RecurringType,      (select sum(BounceEmailStatus) from emailupload where emailupload.FileName = emailcampaign.ToContact collate utf8_unicode_ci) AS BounceEmailNo  FROM     emailcampaign where (Status = 'Running' or Status = 'Paused')) nm_sel_esp"; 
      if (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_sybase))
      { 
          $nmgp_select = "SELECT CampaignName, str_replace (convert(char(10),StartDate,102), '.', '-') + ' ' + convert(char(8),StartDate,20), EmailCount, BounceEmailNo, Status, RecID, Discription, Mode, SmtpProfile, ToContact from (SELECT     RecID,     CampaignName,     Discription,     Status,     Mode,     SmtpProfile,     SmtpServer,     UserName,     Password,     ApiProfile,     SendTo,     ToContact,     MsgType,     Subject,     Message,     FromMail,     FromName,     RecurringDuration,     CreatedBy,     CreatedDate,     ModifiedBy,     ModifiedTime,     ToEmail,     Template,     UploadFile,     EmailSend,     EmailCount,     StartDate,     StartTime,     RecurringType,      (select sum(BounceEmailStatus) from emailupload where emailupload.FileName = emailcampaign.ToContact collate utf8_unicode_ci) AS BounceEmailNo  FROM     emailcampaign where (Status = 'Running' or Status = 'Paused')) nm_sel_esp"; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mysql))
      { 
          $nmgp_select = "SELECT CampaignName, StartDate, EmailCount, BounceEmailNo, Status, RecID, Discription, Mode, SmtpProfile, ToContact from (SELECT     RecID,     CampaignName,     Discription,     Status,     Mode,     SmtpProfile,     SmtpServer,     UserName,     Password,     ApiProfile,     SendTo,     ToContact,     MsgType,     Subject,     Message,     FromMail,     FromName,     RecurringDuration,     CreatedBy,     CreatedDate,     ModifiedBy,     ModifiedTime,     ToEmail,     Template,     UploadFile,     EmailSend,     EmailCount,     StartDate,     StartTime,     RecurringType,      (select sum(BounceEmailStatus) from emailupload where emailupload.FileName = emailcampaign.ToContact collate utf8_unicode_ci) AS BounceEmailNo  FROM     emailcampaign where (Status = 'Running' or Status = 'Paused')) nm_sel_esp"; 
      } 
      else 
      { 
          $nmgp_select = "SELECT CampaignName, StartDate, EmailCount, BounceEmailNo, Status, RecID, Discription, Mode, SmtpProfile, ToContact from (SELECT     RecID,     CampaignName,     Discription,     Status,     Mode,     SmtpProfile,     SmtpServer,     UserName,     Password,     ApiProfile,     SendTo,     ToContact,     MsgType,     Subject,     Message,     FromMail,     FromName,     RecurringDuration,     CreatedBy,     CreatedDate,     ModifiedBy,     ModifiedTime,     ToEmail,     Template,     UploadFile,     EmailSend,     EmailCount,     StartDate,     StartTime,     RecurringType,      (select sum(BounceEmailStatus) from emailupload where emailupload.FileName = emailcampaign.ToContact collate utf8_unicode_ci) AS BounceEmailNo  FROM     emailcampaign where (Status = 'Running' or Status = 'Paused')) nm_sel_esp"; 
      } 
      $nmgp_select .= " " . $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['where_pesq'];
      $nmgp_select_count .= " " . $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['where_pesq'];
      $nmgp_order_by = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['order_grid'];
      $nmgp_select .= $nmgp_order_by; 
      $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nmgp_select_count;
      $rt = $this->Db->Execute($nmgp_select_count);
      if ($rt === false && !$rt->EOF && $GLOBALS["NM_ERRO_IBASE"] != 1)
      {
         $this->Erro->mensagem(__FILE__, __LINE__, "banco", $this->Ini->Nm_lang['lang_errm_dber'], $this->Db->ErrorMsg());
         exit;
      }
      $this->count_ger = $rt->fields[0];
      $rt->Close();
      $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nmgp_select;
      $rs = $this->Db->Execute($nmgp_select);
      if ($rs === false && !$rs->EOF && $GLOBALS["NM_ERRO_IBASE"] != 1)
      {
         $this->Erro->mensagem(__FILE__, __LINE__, "banco", $this->Ini->Nm_lang['lang_errm_dber'], $this->Db->ErrorMsg());
         exit;
      }
      $this->SC_seq_register = 0;
      $PB_tot = (isset($this->count_ger) && $this->count_ger > 0) ? "/" . $this->count_ger : "";
      while (!$rs->EOF)
      {
         $this->SC_seq_register++;
         if (!$this->Ini->sc_export_ajax) {
             $Mens_bar = NM_charset_to_utf8($this->Ini->Nm_lang['lang_othr_prcs']);
             $this->pb->setProgressbarMessage($Mens_bar . ": " . $this->SC_seq_register . $PB_tot);
             $this->pb->addSteps(1);
         }
         $this->Texto_tag .= "<tr>\r\n";
         $this->campaignname = $rs->fields[0] ;  
         $this->startdate = $rs->fields[1] ;  
         $this->emailcount = $rs->fields[2] ;  
         $this->emailcount = (string)$this->emailcount;
         $this->bounceemailno = $rs->fields[3] ;  
         $this->bounceemailno =  str_replace(",", ".", $this->bounceemailno);
         $this->bounceemailno = (string)$this->bounceemailno;
         $this->status = $rs->fields[4] ;  
         $this->recid = $rs->fields[5] ;  
         $this->recid = (string)$this->recid;
         $this->discription = $rs->fields[6] ;  
         $this->mode = $rs->fields[7] ;  
         $this->smtpprofile = $rs->fields[8] ;  
         $this->tocontact = $rs->fields[9] ;  
         $this->sc_proc_grid = true; 
         $_SESSION['scriptcase']['AllCampaignPause']['contr_erro'] = 'on';
 

$check_sql = "SELECT Sum(SendEmail), Sum(BounceEmailStatus) , Sum(OpenEmailStatus), Sum(RespondStatus) FROM emailupload WHERE FileName = '$this->tocontact'";
 
      $nm_select = $check_sql; 
      $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nm_select; 
      $_SESSION['scriptcase']['sc_sql_ult_conexao'] = ''; 
      $this->rs = array();
      if ($SCrx = $this->Db->Execute($nm_select)) 
      { 
          $SCy = 0; 
          $nm_count = $SCrx->FieldCount();
          while (!$SCrx->EOF)
          { 
                 for ($SCx = 0; $SCx < $nm_count; $SCx++)
                 { 
                        $this->rs[$SCy] [$SCx] = $SCrx->fields[$SCx];
                 }
                 $SCy++; 
                 $SCrx->MoveNext();
          } 
          $SCrx->Close();
      } 
      elseif (isset($GLOBALS["NM_ERRO_IBASE"]) && $GLOBALS["NM_ERRO_IBASE"] != 1)  
      { 
          $this->rs = false;
          $this->rs_erro = $this->Db->ErrorMsg();
      } 







if ($this->rs[0][0] == 0)
	{
	$percent = 0;
	$value   = number_format((float)$percent, 2, '.', '');
	$sign    = '%';
	$this->bounce  = 0;
	$bounceper = number_format((float)$this->bounce, 2, '.', '');
	$bounper = 0;
	$bo = number_format((float)$bounper, 2, '.', '');
	$Openper  = 0;
	$Openper  = number_format((float)$Openper, 2, '.', '');
	$resper  = 0;
	$resper  = number_format((float)$resper, 2, '.', '');
	
	
    
	$this->emailnosend  	= $this->rs[0][0];
	$this->noofbounce   	= $this->rs[0][1];
	$this->noofopen       = $this->rs[0][2];
	$this->noofresponse   = $this->rs[0][3];
	$this->bounce  		= "0 $sign";
	$this->sentper      	= "0 $sign";
	$this->boun         	= "0 $sign";
	$this->open 			= "0 $sign";
	$this->totalopen      = "0 $sign";
	$this->responce 	    = "0 $sign";
	$this->totalresponce  = "0 $sign";
}
elseif (isset($this->rs[0][0])) 
{
	
	$percent = ($this->rs[0][0]/$this->emailcount *100);
	$value   = number_format((float)$percent, 2, '.', '');
	$sign    = '%';
	$this->bounce  = ($this->rs[0][1]/$this->emailcount *100);
	$bounceper = number_format((float)$this->bounce, 2, '.', '');
	$bounper = ($this->rs[0][1]/$this->rs[0][0]*100);
	$bo = number_format((float)$bounper, 2, '.', '');
	$Open = ($this->rs[0][2]/$this->rs[0][0]*100);
	$Openper = number_format((float)$Open, 2, '.', '');
	$TotalOpen  = ($this->rs[0][2]/$this->emailcount *100);
	$TotalOpenper = number_format((float)$TotalOpen, 2, '.', '');
	$Responce = ($this->rs[0][3]/$this->rs[0][0]*100);
	$Resper = number_format((float)$Responce, 2, '.', '');
	$TotalResponce  = ($this->rs[0][3]/$this->emailcount *100);
	$TotalResper = number_format((float)$TotalResponce, 2, '.', '');
	
    
	$this->emailnosend  	= $this->rs[0][0];
	$this->noofbounce   	= $this->rs[0][1];
	$this->noofopen       = $this->rs[0][2];
	$this->noofresponse   = $this->rs[0][3];
	$this->bounce  		= "$bounceper $sign";
	$this->sentper      	= "$value $sign";
	$this->boun         	= "$bo $sign"; 
	$this->open 			= "$Openper $sign";
	$this->totalopen      = "$TotalOpenper $sign";
	$this->responce       = "$Resper $sign";
	$this->totalresponce  = "$TotalResper $sign";
}

else    
{
		
		$this->emailnosend  = '';
		$emailstatus  = '';
		$this->sentper      = '';
		$this->bounce       = '';
		$this->boun         = ''; 
		$this->open 		  = '';
		$this->totalopen    = '';
}
$_SESSION['scriptcase']['AllCampaignPause']['contr_erro'] = 'off'; 
         foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['field_order'] as $Cada_col)
         { 
            if (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off")
            { 
                $NM_func_exp = "NM_export_" . $Cada_col;
                $this->$NM_func_exp();
            } 
         } 
         $this->Texto_tag .= "</tr>\r\n";
         $rs->MoveNext();
      }
      $this->Texto_tag .= "</table>\r\n";
      if(isset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['export_sel_columns']['field_order']))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['field_order'] = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['export_sel_columns']['field_order'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['export_sel_columns']['field_order']);
      }
      if(isset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['export_sel_columns']['usr_cmp_sel']))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['usr_cmp_sel'] = $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['export_sel_columns']['usr_cmp_sel'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['export_sel_columns']['usr_cmp_sel']);
      }
      $rs->Close();
   }
   //----- campaignname
   function NM_export_campaignname()
   {
         $this->campaignname = html_entity_decode($this->campaignname, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->campaignname = strip_tags($this->campaignname);
         $this->campaignname = NM_charset_to_utf8($this->campaignname);
         $this->campaignname = str_replace('<', '&lt;', $this->campaignname);
         $this->campaignname = str_replace('>', '&gt;', $this->campaignname);
         $this->Texto_tag .= "<td>" . $this->campaignname . "</td>\r\n";
   }
   //----- startdate
   function NM_export_startdate()
   {
             $conteudo_x =  $this->startdate;
             nm_conv_limpa_dado($conteudo_x, "YYYY-MM-DD");
             if (is_numeric($conteudo_x) && strlen($conteudo_x) > 0) 
             { 
                 $this->nm_data->SetaData($this->startdate, "YYYY-MM-DD  ");
                 $this->startdate = $this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", "ddmmaaaa"));
             } 
         $this->startdate = NM_charset_to_utf8($this->startdate);
         $this->startdate = str_replace('<', '&lt;', $this->startdate);
         $this->startdate = str_replace('>', '&gt;', $this->startdate);
         $this->Texto_tag .= "<td>" . $this->startdate . "</td>\r\n";
   }
   //----- emailcount
   function NM_export_emailcount()
   {
             nmgp_Form_Num_Val($this->emailcount, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->emailcount = NM_charset_to_utf8($this->emailcount);
         $this->emailcount = str_replace('<', '&lt;', $this->emailcount);
         $this->emailcount = str_replace('>', '&gt;', $this->emailcount);
         $this->Texto_tag .= "<td>" . $this->emailcount . "</td>\r\n";
   }
   //----- emailnosend
   function NM_export_emailnosend()
   {
         $this->emailnosend = html_entity_decode($this->emailnosend, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->emailnosend = strip_tags($this->emailnosend);
         $this->emailnosend = NM_charset_to_utf8($this->emailnosend);
         $this->emailnosend = str_replace('<', '&lt;', $this->emailnosend);
         $this->emailnosend = str_replace('>', '&gt;', $this->emailnosend);
         $this->Texto_tag .= "<td>" . $this->emailnosend . "</td>\r\n";
   }
   //----- sentper
   function NM_export_sentper()
   {
             nmgp_Form_Num_Val($this->sentper, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "2", "N", "", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->sentper = NM_charset_to_utf8($this->sentper);
         $this->sentper = str_replace('<', '&lt;', $this->sentper);
         $this->sentper = str_replace('>', '&gt;', $this->sentper);
         $this->Texto_tag .= "<td>" . $this->sentper . "</td>\r\n";
   }
   //----- bounceemailno
   function NM_export_bounceemailno()
   {
             nmgp_Form_Num_Val($this->bounceemailno, $_SESSION['scriptcase']['reg_conf']['grup_val'], $_SESSION['scriptcase']['reg_conf']['dec_val'], "0", "S", "2", "", "V:" . $_SESSION['scriptcase']['reg_conf']['monet_f_pos'] . ":" . $_SESSION['scriptcase']['reg_conf']['monet_f_neg'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['unid_mont_group_digit']) ; 
         $this->bounceemailno = NM_charset_to_utf8($this->bounceemailno);
         $this->bounceemailno = str_replace('<', '&lt;', $this->bounceemailno);
         $this->bounceemailno = str_replace('>', '&gt;', $this->bounceemailno);
         $this->Texto_tag .= "<td>" . $this->bounceemailno . "</td>\r\n";
   }
   //----- bounce
   function NM_export_bounce()
   {
             nmgp_Form_Num_Val($this->bounce, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "2", "N", "", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->bounce = NM_charset_to_utf8($this->bounce);
         $this->bounce = str_replace('<', '&lt;', $this->bounce);
         $this->bounce = str_replace('>', '&gt;', $this->bounce);
         $this->Texto_tag .= "<td>" . $this->bounce . "</td>\r\n";
   }
   //----- boun
   function NM_export_boun()
   {
             nmgp_Form_Num_Val($this->boun, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "2", "N", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->boun = NM_charset_to_utf8($this->boun);
         $this->boun = str_replace('<', '&lt;', $this->boun);
         $this->boun = str_replace('>', '&gt;', $this->boun);
         $this->Texto_tag .= "<td>" . $this->boun . "</td>\r\n";
   }
   //----- noofopen
   function NM_export_noofopen()
   {
         $this->noofopen = html_entity_decode($this->noofopen, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->noofopen = strip_tags($this->noofopen);
         $this->noofopen = NM_charset_to_utf8($this->noofopen);
         $this->noofopen = str_replace('<', '&lt;', $this->noofopen);
         $this->noofopen = str_replace('>', '&gt;', $this->noofopen);
         $this->Texto_tag .= "<td>" . $this->noofopen . "</td>\r\n";
   }
   //----- open
   function NM_export_open()
   {
             nmgp_Form_Num_Val($this->open, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "2", "S", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->open = NM_charset_to_utf8($this->open);
         $this->open = str_replace('<', '&lt;', $this->open);
         $this->open = str_replace('>', '&gt;', $this->open);
         $this->Texto_tag .= "<td>" . $this->open . "</td>\r\n";
   }
   //----- totalopen
   function NM_export_totalopen()
   {
             nmgp_Form_Num_Val($this->totalopen, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "2", "S", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->totalopen = NM_charset_to_utf8($this->totalopen);
         $this->totalopen = str_replace('<', '&lt;', $this->totalopen);
         $this->totalopen = str_replace('>', '&gt;', $this->totalopen);
         $this->Texto_tag .= "<td>" . $this->totalopen . "</td>\r\n";
   }
   //----- noofresponse
   function NM_export_noofresponse()
   {
         $this->noofresponse = html_entity_decode($this->noofresponse, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->noofresponse = strip_tags($this->noofresponse);
         $this->noofresponse = NM_charset_to_utf8($this->noofresponse);
         $this->noofresponse = str_replace('<', '&lt;', $this->noofresponse);
         $this->noofresponse = str_replace('>', '&gt;', $this->noofresponse);
         $this->Texto_tag .= "<td>" . $this->noofresponse . "</td>\r\n";
   }
   //----- responce
   function NM_export_responce()
   {
             nmgp_Form_Num_Val($this->responce, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "2", "S", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->responce = NM_charset_to_utf8($this->responce);
         $this->responce = str_replace('<', '&lt;', $this->responce);
         $this->responce = str_replace('>', '&gt;', $this->responce);
         $this->Texto_tag .= "<td>" . $this->responce . "</td>\r\n";
   }
   //----- totalresponce
   function NM_export_totalresponce()
   {
             nmgp_Form_Num_Val($this->totalresponce, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "2", "S", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->totalresponce = NM_charset_to_utf8($this->totalresponce);
         $this->totalresponce = str_replace('<', '&lt;', $this->totalresponce);
         $this->totalresponce = str_replace('>', '&gt;', $this->totalresponce);
         $this->Texto_tag .= "<td>" . $this->totalresponce . "</td>\r\n";
   }
   //----- status
   function NM_export_status()
   {
         $this->status = html_entity_decode($this->status, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->status = strip_tags($this->status);
         $this->status = NM_charset_to_utf8($this->status);
         $this->status = str_replace('<', '&lt;', $this->status);
         $this->status = str_replace('>', '&gt;', $this->status);
         $this->Texto_tag .= "<td>" . $this->status . "</td>\r\n";
   }
   //----- recid
   function NM_export_recid()
   {
             nmgp_Form_Num_Val($this->recid, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         $this->recid = NM_charset_to_utf8($this->recid);
         $this->recid = str_replace('<', '&lt;', $this->recid);
         $this->recid = str_replace('>', '&gt;', $this->recid);
         $this->Texto_tag .= "<td>" . $this->recid . "</td>\r\n";
   }
   //----- discription
   function NM_export_discription()
   {
         $this->discription = html_entity_decode($this->discription, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->discription = strip_tags($this->discription);
         $this->discription = NM_charset_to_utf8($this->discription);
         $this->discription = str_replace('<', '&lt;', $this->discription);
         $this->discription = str_replace('>', '&gt;', $this->discription);
         $this->Texto_tag .= "<td>" . $this->discription . "</td>\r\n";
   }
   //----- mode
   function NM_export_mode()
   {
         $this->mode = html_entity_decode($this->mode, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->mode = strip_tags($this->mode);
         $this->mode = NM_charset_to_utf8($this->mode);
         $this->mode = str_replace('<', '&lt;', $this->mode);
         $this->mode = str_replace('>', '&gt;', $this->mode);
         $this->Texto_tag .= "<td>" . $this->mode . "</td>\r\n";
   }
   //----- smtpprofile
   function NM_export_smtpprofile()
   {
         $this->smtpprofile = html_entity_decode($this->smtpprofile, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->smtpprofile = strip_tags($this->smtpprofile);
         $this->smtpprofile = NM_charset_to_utf8($this->smtpprofile);
         $this->smtpprofile = str_replace('<', '&lt;', $this->smtpprofile);
         $this->smtpprofile = str_replace('>', '&gt;', $this->smtpprofile);
         $this->Texto_tag .= "<td>" . $this->smtpprofile . "</td>\r\n";
   }

   //----- 
   function grava_arquivo_rtf()
   {
      global $nm_lang, $doc_wrap;
      $this->Rtf_f = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $rtf_f       = fopen($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo, "w");
      require_once($this->Ini->path_third      . "/rtf_new/document_generator/cl_xml2driver.php"); 
      $text_ok  =  "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n"; 
      $text_ok .=  "<DOC config_file=\"" . $this->Ini->path_third . "/rtf_new/doc_config.inc\" >\r\n"; 
      $text_ok .=  $this->Texto_tag; 
      $text_ok .=  "</DOC>\r\n"; 
      $xml = new nDOCGEN($text_ok,"RTF"); 
      fwrite($rtf_f, $xml->get_result_file());
      fclose($rtf_f);
   }

   function nm_conv_data_db($dt_in, $form_in, $form_out)
   {
       $dt_out = $dt_in;
       if (strtoupper($form_in) == "DB_FORMAT") {
           if ($dt_out == "null" || $dt_out == "")
           {
               $dt_out = "";
               return $dt_out;
           }
           $form_in = "AAAA-MM-DD";
       }
       if (strtoupper($form_out) == "DB_FORMAT") {
           if (empty($dt_out))
           {
               $dt_out = "null";
               return $dt_out;
           }
           $form_out = "AAAA-MM-DD";
       }
       if (strtoupper($form_out) == "SC_FORMAT_REGION") {
           $this->nm_data->SetaData($dt_in, strtoupper($form_in));
           $prep_out  = (strpos(strtolower($form_in), "dd") !== false) ? "dd" : "";
           $prep_out .= (strpos(strtolower($form_in), "mm") !== false) ? "mm" : "";
           $prep_out .= (strpos(strtolower($form_in), "aa") !== false) ? "aaaa" : "";
           $prep_out .= (strpos(strtolower($form_in), "yy") !== false) ? "aaaa" : "";
           return $this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", $prep_out));
       }
       else {
           nm_conv_form_data($dt_out, $form_in, $form_out);
           return $dt_out;
       }
   }
   function progress_bar_end()
   {
      unset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_file']);
      if (is_file($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_file'] = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      }
      $path_doc_md5 = md5($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause'][$path_doc_md5][0] = $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause'][$path_doc_md5][1] = $this->Tit_doc;
      $Mens_bar = $this->Ini->Nm_lang['lang_othr_file_msge'];
      if ($_SESSION['scriptcase']['charset'] != "UTF-8") {
          $Mens_bar = sc_convert_encoding($Mens_bar, "UTF-8", $_SESSION['scriptcase']['charset']);
      }
      $this->pb->setProgressbarMessage($Mens_bar);
      $this->pb->setDownloadLink($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $this->pb->setDownloadMd5($path_doc_md5);
      $this->pb->completed();
   }
   //---- 
   function monta_html()
   {
      global $nm_url_saida, $nm_lang;
      include($this->Ini->path_btn . $this->Ini->Str_btn_grid);
      unset($_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_file']);
      if (is_file($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause']['rtf_file'] = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      }
      $path_doc_md5 = md5($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause'][$path_doc_md5][0] = $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $_SESSION['sc_session'][$this->Ini->sc_page]['AllCampaignPause'][$path_doc_md5][1] = $this->Tit_doc;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML<?php echo $_SESSION['scriptcase']['reg_conf']['html_dir'] ?>>
<HEAD>
 <TITLE>Pause All Campaign :: RTF</TITLE>
 <META http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['scriptcase']['charset_html'] ?>" />
<?php
if ($_SESSION['scriptcase']['proc_mobile'])
{
?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<?php
}
?>
  <META http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT"/>
  <META http-equiv="Last-Modified" content="<?php echo gmdate("D, d M Y H:i:s"); ?> GMT"/>
  <META http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate"/>
  <META http-equiv="Cache-Control" content="post-check=0, pre-check=0"/>
  <META http-equiv="Pragma" content="no-cache"/>
 <link rel="shortcut icon" href="../_lib/img/grp__NM__ico__NM__logo.png">
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export.css" /> 
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export<?php echo $_SESSION['scriptcase']['reg_conf']['css_dir'] ?>.css" /> 
 <?php
 if(isset($this->Ini->str_google_fonts) && !empty($this->Ini->str_google_fonts))
 {
 ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->Ini->str_google_fonts ?>" />
 <?php
 }
 ?>
  <link rel="stylesheet" type="text/css" href="../_lib/buttons/<?php echo $this->Ini->Str_btn_css ?>" /> 
</HEAD>
<BODY class="scExportPage">
<?php echo $this->Ini->Ajax_result_set ?>
<table style="border-collapse: collapse; border-width: 0; height: 100%; width: 100%"><tr><td style="padding: 0; text-align: center; vertical-align: middle">
 <table class="scExportTable" align="center">
  <tr>
   <td class="scExportTitle" style="height: 25px">RTF</td>
  </tr>
  <tr>
   <td class="scExportLine" style="width: 100%">
    <table style="border-collapse: collapse; border-width: 0; width: 100%"><tr><td class="scExportLineFont" style="padding: 3px 0 0 0" id="idMessage">
    <?php echo $this->Ini->Nm_lang['lang_othr_file_msge'] ?>
    </td><td class="scExportLineFont" style="text-align:right; padding: 3px 0 0 0">
     <?php echo nmButtonOutput($this->arr_buttons, "bexportview", "document.Fview.submit()", "document.Fview.submit()", "idBtnView", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bdownload", "document.Fdown.submit()", "document.Fdown.submit()", "idBtnDown", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bvoltar", "document.F0.submit()", "document.F0.submit()", "idBtnBack", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "", "");
 ?>
    </td></tr></table>
   </td>
  </tr>
 </table>
</td></tr></table>
<form name="Fview" method="get" action="<?php echo $this->Ini->path_imag_temp . "/" . $this->Arquivo ?>" target="_blank" style="display: none"> 
</form>
<form name="Fdown" method="get" action="AllCampaignPause_download.php" target="_blank" style="display: none"> 
<input type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<input type="hidden" name="nm_tit_doc" value="AllCampaignPause"> 
<input type="hidden" name="nm_name_doc" value="<?php echo $path_doc_md5 ?>"> 
</form>
<FORM name="F0" method=post action="./"> 
<INPUT type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<INPUT type="hidden" name="nmgp_opcao" value="volta_grid"> 
</FORM> 
</BODY>
</HTML>
<?php
   }
   function nm_gera_mask(&$nm_campo, $nm_mask)
   { 
      $trab_campo = $nm_campo;
      $trab_mask  = $nm_mask;
      $tam_campo  = strlen($nm_campo);
      $trab_saida = "";
      $str_highlight_ini = "";
      $str_highlight_fim = "";
      if(substr($nm_campo, 0, 23) == '<div class="highlight">' && substr($nm_campo, -6) == '</div>')
      {
           $str_highlight_ini = substr($nm_campo, 0, 23);
           $str_highlight_fim = substr($nm_campo, -6);

           $trab_campo = substr($nm_campo, 23, -6);
           $tam_campo  = strlen($trab_campo);
      }      $mask_num = false;
      for ($x=0; $x < strlen($trab_mask); $x++)
      {
          if (substr($trab_mask, $x, 1) == "#")
          {
              $mask_num = true;
              break;
          }
      }
      if ($mask_num )
      {
          $ver_duas = explode(";", $trab_mask);
          if (isset($ver_duas[1]) && !empty($ver_duas[1]))
          {
              $cont1 = count(explode("#", $ver_duas[0])) - 1;
              $cont2 = count(explode("#", $ver_duas[1])) - 1;
              if ($tam_campo >= $cont2)
              {
                  $trab_mask = $ver_duas[1];
              }
              else
              {
                  $trab_mask = $ver_duas[0];
              }
          }
          $tam_mask = strlen($trab_mask);
          $xdados = 0;
          for ($x=0; $x < $tam_mask; $x++)
          {
              if (substr($trab_mask, $x, 1) == "#" && $xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_campo, $xdados, 1);
                  $xdados++;
              }
              elseif ($xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_mask, $x, 1);
              }
          }
          if ($xdados < $tam_campo)
          {
              $trab_saida .= substr($trab_campo, $xdados);
          }
          $nm_campo = $str_highlight_ini . $trab_saida . $str_highlight_ini;
          return;
      }
      for ($ix = strlen($trab_mask); $ix > 0; $ix--)
      {
           $char_mask = substr($trab_mask, $ix - 1, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               $trab_saida = $char_mask . $trab_saida;
           }
           else
           {
               if ($tam_campo != 0)
               {
                   $trab_saida = substr($trab_campo, $tam_campo - 1, 1) . $trab_saida;
                   $tam_campo--;
               }
               else
               {
                   $trab_saida = "0" . $trab_saida;
               }
           }
      }
      if ($tam_campo != 0)
      {
          $trab_saida = substr($trab_campo, 0, $tam_campo) . $trab_saida;
          $trab_mask  = str_repeat("z", $tam_campo) . $trab_mask;
      }
   
      $iz = 0; 
      for ($ix = 0; $ix < strlen($trab_mask); $ix++)
      {
           $char_mask = substr($trab_mask, $ix, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               if ($char_mask == "." || $char_mask == ",")
               {
                   $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
               }
               else
               {
                   $iz++;
               }
           }
           elseif ($char_mask == "x" || substr($trab_saida, $iz, 1) != "0")
           {
               $ix = strlen($trab_mask) + 1;
           }
           else
           {
               $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
           }
      }
      $nm_campo = $str_highlight_ini . $trab_saida . $str_highlight_ini;
   } 
}

?>
