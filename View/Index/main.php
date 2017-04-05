<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div style="background-color: #ecf0f5;height: 100%" id="app">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">任务列表</h3>
                            <section>

                            </section>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>任务名称</th>
                                        <th>队列</th>
                                        <th>数据</th>
                                        <th>重试次数</th>
                                        <th>可用时间</th>
                                        <th>取出时间</th>
                                        <th>状态</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in items">
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.queue }}</td>
                                        <td>{{ item.payload }}</td>
                                        <td>{{ item.attempts }}</td>
                                        <td>{{ item.available_at | DateFormat }}</td>
                                        <td>{{ item.reserved_at | DateFormat }}</td>
                                        <td>{{ item.status | JobStatus }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- 分页 -->
                            <div class="row" style="padding-top: 10px;">
                                <div class="col-sm-12">
                                    <div class="dataTables_paginate paging_simple_numbers" >
                                        <button class="btn btn-primary" @click="prePage">前一页</button>
                                        <div style="display: inline;font-size: 16px;margin-left: 10px;margin-right: 10px;"><span>{{ page }}</span> / <span>{{ pageCount }}</span></div>
                                        <button class="btn btn-primary" @click="nextPage">下一页</button>
                                        <input type="text" class="form-control input-sm" style="width: 70px;display: inline" placeholder="跳转页码" v-model="redirect_page">
                                        <button class="btn btn-primary" @click="goPage">GO</button>
                                    </div>
                                </div>
                            </div>
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
    </div>

    <script>
        $(document).ready(function(){
            var App = new Vue({
                el: '#app',
                data:{
                    page: 1,
                    limit: 20,
                    amount: 0,
                    items: [],
                    redirect_page: '', //跳转页
                    filter: {},
                    operator: {},
                    value: {},
                },
                mounted: function(){
                    this.getJobList();
                },
                filters: {
                    JobStatus: function(val){
                        val = parseInt(val);
                        switch (val){
                            //'任务状态：0排队中,1工作中,2已完成,3异常',
                            case 0:
                                val =  '排队中';
                                break;
                            case 1:
                                val = '工作中';
                                break;
                            case 2:
                                val = '已完成';
                                break;
                            case 3:
                                val = '异常';
                                break;
                        }
                        return val;
                    },
                    /**
                     *
                     * @param val
                     * @returns {string}
                     * @constructor
                     */
                    DateFormat: function(val){
                        if(val == 0){
                            return '--';
                        }
                        var date = new Date(parseInt(val)*1000);
                        return date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
                    }
                },
                methods: {
                    getJobList: function(){
                        if (this.urlObject) {
                            this.page =  this.urlObject.search.page || this.page;
                        }
                        if (this.urlObject) {
                            this.limit =  this.urlObject.search.limit || this.limit;
                        }
                        this.doRequest();
                    },
                    //请求获取数据列表
                    doRequest: function () {
                        var that = this;
                        $.ajax({
                            url: that.requestURL,
                            type: 'get',
                            dataType: 'json',
                            success: function (res) {
                                if (res.status) {
                                    that.items = res.data.items;
                                    that.amount = res.data.amount;
                                    that.page = res.data.page;
                                    that.limit = res.data.limit;
                                    that.redirect_page = ''; //清空
                                } else {
                                    layer.msg(res.msg);
                                }
                            }, error: function () {
                                layer.msg('网络繁忙，请稍后再试')
                            }
                        });
                    },
                    //去搜索
                    doSearch: function () {
                        this.redirect_page = 1;
                        this.doRequest();
                    },
                    //前一页
                    prePage: function(){
                        if(this.page - 1 > 0){
                            this.redirect_page = this.page - 1;
                            this.goPage();
                        }else{
                            if(layer){
                                layer.msg('已经是第一页了');
                            }else{
                                alert('已经是第一页了');
                            }
                        }
                    },
                    //下一页
                    nextPage: function(){
                        if(this.page + 1 <= this.pageCount){
                            this.redirect_page = this.page + 1;
                            this.goPage();
                        }else{
                            if(layer){
                                layer.msg('已经是最后一页了');
                            }else{
                                alert('已经是最后一页了');
                            }
                        }

                    },
                    //跳转到指定页面
                    goPage: function(){
                        this.doRequest();
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
                computed: {
                    //总页码
                    pageCount: function () {
                        return Math.ceil(this.amount / this.limit);
                    },
                    //解析URL后的对象
                    urlObject: function () {
                        return this.parserUrl(location.href);
                    },
                    queryString: function(){
                        var index = 0;
                        var result = [];
                        //三段式
                        for (var alias in this.filter) {
                            if (!this.value[alias]) {
                                continue;
                            }

                            var key = this.filter[alias];
                            var tmp = '_filter[' + index + ']=' + key;
                            tmp += '&_operator[' + index + ']=' + this.operator[alias];
                            tmp += '&_value[' + index + ']=' + this.value[alias];
                            result.push(tmp);
                            index++;
                        }
                        //页码，优先采用跳转页码
                        var page = this.redirect_page || this.page;
                        result.push('page=' + page);
                        result.push('limit=' + this.limit);

                        return result.join('&');
                    },
                    //搜索的URL
                    requestURL: function () {
                        return 'index.php?g=Queue&m=Job&a=lists&' + this.queryString;
                    }

                }
            });
        });

    </script>


</block>