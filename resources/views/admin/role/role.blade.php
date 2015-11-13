<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>

<form method="post" action="{{  url('admin/role')  }}">
    {!! csrf_field() !!}
    <input type="text" name="name" />
    <input type="submit" />
</form>

</body>
</html>