<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div style="background-color: #ecf0f5;height: 100%;display: none;" id="app">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">重新入列设置</h3>
                            <section style="margin-top: 8px;">
                                <div class="row">

                                </div>

                            </section>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <form class="form-horizontal">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">任务</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" disabled value="{$job['name']}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">延迟执行时间(单位：秒)</label>

                                        <div class="col-sm-10">
                                            <input type="number" step="1" min="0" class="form-control" v-model="delay">
                                        </div>
                                    </div>

                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary pull-left" @click="doRepush">确认</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->


                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->

        <section style="display: none">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                        </div>
                    </div>
                </div>
            </section>
        </section>




    </div>

    <script>
        $(document).ready(function(){
            var App = new Vue({
                el: '#app',
                data:{
                    job_id: "{$job['id']}",
                    delay: 0
                },
                mounted: function(){
                    $('#app').show();
                },
                methods: {
                    //关闭layer窗口
                    closeLayer: function(){
                        var index = window.parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);
                    },
                    //请求获取数据列表
                    doRepush: function () {
                        var that = this;
                        var data = {
                            job_id: that.job_id,
                            delay: that.delay
                        };
                        $.ajax({
                            url: "{:U('Queue/Manage/doRepush')}",
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            success: function (res) {
                                if (res.status) {
                                    layer.msg('操作完成');
                                    setTimeout(function(){
                                        that.closeLayer();
                                    }, 700);

                                } else {
                                    layer.msg(res.msg);
                                }
                            }, error: function () {
                                layer.msg('网络繁忙，请稍后再试')
                            }
                        });
                    },

                    /**
                     * 解析url
                     * @param url
                     * @returns {{protocol, host: (*|string), hostname: (*|string), pathname: (*|string), search: {}, hash}}
                     */
                    parserUrl: function (url) {
                        var a = document.createElement('a');
                        a.href = url;

                        var search = function (search) {
                            if (!search) return {};

                            var ret = {};
                            search = search.slice(1).split('&');
                            for (var i = 0, arr; i < search.length; i++) {
                                arr = search[i].split('=');
                                var key = arr[0], value = arr[1];
                                if (/\[\]$/.test(key)) {
                                    ret[key] = ret[key] || [];
                                    ret[key].push(value);
                                } else {
                                    ret[key] = value;
                                }
                            }
                            return ret;
                        };

                        return {
                            protocol: a.protocol,
                            host: a.host,
                            hostname: a.hostname,
                            pathname: a.pathname,
                            search: search(a.search),
                            hash: a.hash
                        }
                    },
                },
            });
        });

    </script>


</block>