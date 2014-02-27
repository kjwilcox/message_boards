<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en-US">
 <head>
  <meta name="google-site-verification" content="mlwQqXsMPPQHK0KrZ5R4HVUX_NoJx2qCUNLgFR6x8z4" />
  <title>Kwilco.net &raquo; <?php if (isset($title)) { echo $title; } ?></title>
  <link rel="stylesheet" type="text/css" href="style.php">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!--.png fix + CSS fix for IE < 7 -->
  <!--[if lt IE 7]>
   <script defer type="text/javascript" src="ie7/pngfix.js"></script>
   <style type="text/css">
    #sidecol { width: 207px; }
    #submit { width: 35px; }
   </style>
  <![endif]-->
 </head>
 <body class="$id">
  <div id="header">
   <h1>Kwilco.net &raquo; <?php if (isset($title)) { echo $title; } ?></h1>
   <p>Pointless Archive Site</p>
  </div>
  <div id="navigation">
   <h2>Navigation</h2>
    <ul
    ><li class="home" title="The home page."><a href="index">Home</a></li
    ><li class="about" title="About the site creator."><a href="about">About</a></li
    ><li class="projects" title="Random projects I've made."><a href="projects">Projects</a></li
    ><li class="articles" title="Articles I've written."><a href="articles">Articles</a></li
    ><li title="Message boards."><a href="http://kwilco.net/board/">Boards</a></li
   ></ul>
  </div>
  <div id="sidecol">
   <?php /*<form method="post" action="log.php" id="login">
    <fieldset>
     <legend>Login</legend
    ><input type="text" id="username" name="username" value=""
   /><input type="password" id="password" name="password" value=""
   /><input type="submit" id="submit"  value="Log" />
    </fieldset>
   </form>*/ ?><?php
	if (isset($nav)) { echo "\n", $nav, "\n"; }   
    ?><h2>Stylesheet</h2>
    <ul>
     <li><a href="style?style=normal">Fixed Width</a></li>
     <li><a href="style?style=scale">Scalable Width</a></li>
     <li><a href="style?style=blind">High Contrast</a></li>
    </ul>
   </div>
