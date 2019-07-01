{%extend@common/main%}
{%block@title%}
<title><?=$site_title?></title>
<meta name="keywords" content="<?=$site_keywords?>">
<meta name="description" content="<?=$site_description?>">
<link rel="canonical" href="<?=$site_url?>">
{%end%}

{%block@article%}
{%include@weixinqun/index%}
{%end%}