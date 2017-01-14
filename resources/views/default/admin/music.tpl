{include file='admin/main.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            用户列表
            <small>User List</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div id="msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
            </div>
        </div>
        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="mid" type="number" placeholder="歌曲mid" class="form-control">
            </div>
            <div class="form-group">
                <input name="name" type="name" placeholder="歌曲名" class="form-control">
            </div>
            <div class="form-group">
                <input name="author" placeholder="歌手" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
            </div>
        </form>
        <hr>
        <fieldset class="form-inline margin-bottom">
            <div class="form-group">
                <input type="number" id="mid" placeholder="歌曲mid" class="form-control">
            </div>
            <div class="form-group">
                <input type="text" id="name" placeholder="歌曲名" class="form-control">
            </div>
            <div class="form-group">
                <input type="text" id="author" placeholder="歌手" class="form-control">
            </div>
            <div class="form-group">
                <button id="insert" class="form-control btn btn-default btn-flat" onclick="insert()">添加</button>
            </div>
        </fieldset>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive">
                        {$music->render()}
                        <table class="table table-hover">
                            <tr>
                                <th>操作</th>
                                <th>id</th>
                                <th>mid</th>
                                <th>歌名</th>
                                <th>歌手</th>
                            </tr>
                            {foreach $music as $m}
                            <tr>
                                <td>
                                    <a class="btn btn-danger btn-sm" id="delete" value="{$m->mid}" href="javascript:void(0);" onclick="confirm_delete({$m->mid});">删除</a>
                                </td>
                                <td>#{$m->id}</td>
                                <td>{$m->mid}</td>
                                <td>{$m->name}</td>
                                <td>{$m->author}</td>
                            </tr>
                            {/foreach}
                        </table>
                        {$music->render()}
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">
    function insert() {
        $.ajax({
            type: "POST",
            url: "/admin/music",
            dataType: "json",
            data: {
                mid: $("#mid").val(),
                name: $("#name").val(),
                author: $("#author").val()
            },
            success: function (data) {
                if (data.ret) {
                    $("#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                    window.setTimeout("location.reload()", 2000);
                } else {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html(data.msg);
                }
            },
            error: function (jqXHR) {
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误：" + jqXHR.status);
            }
        });
    }

    function deletemusic(mid){
        $.ajax({
            type:"DELETE",
            url:"/admin/music/" + mid,
            dataType:"json",
            success:function(data){
                if(data.ret){
                    $("#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                    window.setTimeout("location.href='/admin/music'", 2000);
                }else{
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html(data.msg);
                }
            },
            error:function(jqXHR){
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误："+jqXHR.status);
            }
        });
    }
    function confirm_delete(mid) {
        $.confirm({
            title: '确认操作',
            content: '你确定要删除这首歌?',
            confirm: function(){
                deletemusic(mid);
            },
            confirmButton: '是',
            cancelButton: '否',
            theme: 'black'
        });
    }
    $(document).ready(function(){
        $("html").keydown(function(event){
            if(event.keyCode==13){
                $(form).summit();
            }
        });
        $("#ok-close").click(function(){
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function(){
            $("#msg-error").hide(100);
        });
    })
</script>

{include file='admin/footer.tpl'}