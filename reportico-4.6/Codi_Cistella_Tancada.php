   <HTML>
      <BODY>
          <?php
             // set error reporting level
  //error_reporting(E_ALL);

    // Set the timezone according to system defaults
    date_default_timezone_set(@date_default_timezone_get());
  require_once('reportico.php');

    // Only turn on output buffering if necessary, normally leave this uncommented
  //ob_start();

  $q = new reportico();

            $q->initial_project = "Aplicoop";
            $q->initial_project_password = "Massadas";
            $q->initial_report = "cistella_tancada.xml";
            $q->initial_execute_mode = "EXECUTE";
            $q->initial_output_format = "HTML";
            $q->initial_show_detail = "show";
    $q->initial_show_graph = "hide";
    $q->initial_show_group_headers = "hide";
    $q->initial_show_group_trailers = "hide";
    $q->initial_show_column_headers = "hide";
    $q->initial_show_criteria = "hide";
     $q->bootstrap_styles = false;
    
            $q->initial_output_style = "TABLE";
            $q->access_mode = "REPORTOUTPUT";
 //           $q->embedded_report = true;
            $q->execute();
          ?> 

      </BODY>
    </HTML>