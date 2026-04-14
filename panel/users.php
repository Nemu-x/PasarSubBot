<?php
require_once __DIR__ . '/i18n.php';
session_start();
require_once __DIR__ . '/../config.php';
panelSyncLangFromSetting();
$query = $pdo->prepare("SELECT * FROM admin WHERE username=:username");
    $query->bindParam("username", $_SESSION["user"], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $query = $pdo->prepare("SELECT * FROM user");
    $query->execute();
    $listusers = $query->fetchAll();
if( !isset($_SESSION["user"]) || !$result ){
    header('Location: login.php');
    return;
}
$__panelHtml = panelHtmlAttrs();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($__panelHtml['lang']); ?>" dir="<?php echo htmlspecialchars($__panelHtml['dir']); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="img/favicon.html">

    <title><?php echo htmlspecialchars(panelT('page_title')); ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
    <link rel="stylesheet" href="css/owl.carousel.css" type="text/css">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <link href="css/panel-i18n.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
  </head>


<body class="panel-lang-<?php echo htmlspecialchars(panelCurrentLanguage()); ?>">

    <section id="container" class="">
<?php include("header.php");
?>
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                <!-- page start-->
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading"><?php echo htmlspecialchars(panelT('users_list')); ?></header>
                            <table class="table table-striped border-top" id="sample_1">
                                <thead>
                                    <tr>
                                        <th style="width: 8px;">
                                            <input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_numeric_id')); ?></th>
                                        <th><?php echo htmlspecialchars(panelT('col_username')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_phone')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_balance')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_affiliate_count')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_user_status')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_manage')); ?></th>
                                    </tr>
                                </thead>
                                <tbody> <?php
                                foreach($listusers as $list){
                                    $status_user = panelUserStatusLabel($list['User_Status']);
                                    if($list['number'] == "none")$list['number'] = panelT('user_no_phone');
                                   echo "<tr class=\"odd gradeX\">
                                        <td>
                                        <input type=\"checkbox\" class=\"checkboxes\" value=\"1\" /></td>
                                        <td>{$list['id']}</td>
                                        <td class=\"hidden-phone\">{$list['username']}</td>
                                        <td class=\"hidden-phone\">{$list['number']}</td>
                                        <td class=\"hidden-phone\">{$list['Balance']}</td>
                                        <td class=\"hidden-phone\">{$list['affiliatescount']}</td>
                                        <td class=\"hidden-phone\">$status_user</td>
                                        <td class=\"hidden-phone\">
                                        <a class = \"btn btn-success\" href= \"user.php?id={$list['id']}\">" . htmlspecialchars(panelT('btn_user_manage'), ENT_QUOTES, 'UTF-8') . "</a></td>
                                    </tr>";
                                }
                                    ?>
                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
                <!-- page end-->
            </section>
        </section>
        <!--main content end-->
    </section>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.scrollTo.min.js"></script>
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/data-tables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="assets/data-tables/DT_bootstrap.js"></script>


    <!--common script for all pages-->
    <script src="js/common-scripts.js"></script>

    <!--script for this page only-->
    <script>window.__PANEL_DT_LANG = <?php echo panelDataTablesLanguageJson(); ?>;</script>
    <script src="js/dynamic-table.js"></script>


</body>
</html>
