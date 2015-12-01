<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<h1>模拟POST发送页面 www.bpicms.com</h1>
-开发用
<form action="{{ url('api/v1') }}" method="post" enctype="Multipart/form-data" autocomplete="off">
    <input type="hidden" name="_method" class="hidden" value="PUT" />
    请求的方式:<select class="type">
        <option value="get">get</option>
        <option value="post" selected>post</option>
        <option value="put">put</option>
        <option value="delete">delete</option>
    </select><br/>
    请求的模块:<input class="m"/><br/>
    请求的参数:<br/>
    *标准json格式:{"account":"wholesalers","password":"123456"}<br/>
    <textarea class="param" style="width:600px; height:100px"></textarea>
    <input type="submit"/>
</form>
<br/>
<br/>
<br/>
</body>
</html>

<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('form').on('submit', function () {
            var type = $('select.type').val() || 'post';
            var m = $('input.m').val();
            var obj = $(this);
            var action = obj.attr('action');
            var param = obj.find('textarea').val();
            if (param) {
                param = eval('(' + param + ')');
                var input = '';
                for (var i in param) {
                    input += '<input type="hidden" name=' + i + ' value="' + param[i] + '" />'
                }
                obj.append(input);
            }
            if(type == 'post' || type == 'get') {
                $(this).attr('method', type);
                $('.hidden').val(type)
            }else {
                $(this).attr('method', 'post');
                $('.hidden').val(type)
            }

            $(this).attr('action', action + '/' + m);
        })
    })
</script>
