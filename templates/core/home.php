<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Currere Development Page</title>
</head>
<body>
<h1>Currere Demo Page</h1>
<p><a href="<?=$this->e($this->queryToUri('/?controller=Authentication&connector=Strava&action=authenticate'));?>">Connect Strava</a></p>
</body>
</html>