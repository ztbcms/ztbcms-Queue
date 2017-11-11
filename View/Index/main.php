<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div style="background-color: #ecf0f5;height: 100%" id="app" v-cloak>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">任务列表</h3>
                            <section style="margin-top: 8px;">
                                <div class="row">

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">任务名称</span>
                                                <input type="text" class="form-control" placeholder="" name="phone" v-model="value.name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">队列</span>
                                                <input type="text" class="form-control" placeholder="" name="phone" v-model="value.queue">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">当前状态</span>
                                                <select name="status" class="form-control" v-model="value.status">
                                                    <option value="">全部</option>
                                                    <option value="0">排队中</option>
                                                    <option value="1">工作中</option>
                                                    <option value="2">已完成</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <button class="btn btn-success" @click="doSearch">确认</button>
                                    </div>

                                </div>

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
                                        <th>创建时间</th>
                                        <th>可用时间</th>
                                        <th>出队时间</th>
                                        <th>开始时间</th>
                                        <th>结束时间</th>
                                        <th>耗时</th>
                                        <th>当前状态</th>
                                        <th>执行结果</th>
                                        <th style="text-align: center">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in items">
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.queue }}</td>
                                        <td>{{ item.payload }}</td>
                                        <td>{{ item.attempts }}</td>
                                        <td>{{ item.create_time | DateFormat }}</td>
                                        <td>{{ item.available_at | DateFormat }}</td>
                                        <td>{{ item.reserved_at | DateFormat }}</td>
                                        <td>{{ item.start_time | DateFormat }}</td>
                                        <td>{{ item.end_time | DateFormat }}</td>
                                        <td>{{ item.end_time - item.start_time | HandleNumber }} ms</td>
                                        <td>
                                            <template v-if="item.status == 0">
                                                <span class="label label-warning">排队中</span>
                                            </template>
                                            <template v-if="item.status == 1">
                                                <span class="label label-info">工作中</span>
                                            </template>
                                            <template v-if="item.status == 2">
                                                <span class="label label-success">已完成</span>
                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="item.result == 0">
                                                <span class="label label-warning">未执行</span>
                                            </template>
                                            <template v-if="item.result == 1">
                                                <span class="label label-success">成功</span>
                                            </template>
                                            <template v-if="item.result == 2">
                                                <span class="label label-danger">失败</span>
                                            </template>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary" @click="repush(item.id)">重新入列</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- 分页 -->
                            <div class="row" style="padding-top: 10px;">
                                <div class="col-sm-12">
                                    <div class="dataTables_paginate paging_simple_numbers" >
                                        <button class="btn btn-primary" @click="prePage">前一页</button>
                                        <div style="display: inline;font-size: 16px;margin-left: 10px;margin-right: 10px;"><span>{{ page }}</span> / <span>{{ pageCount }}</span><span> 总数: {{ amount }}</span></div>
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
                    filter: {
                        'name' : 'name',
                        'queue' : 'queue',
                        'status' : 'status'
                    },
                    operator: {
                        'name' : 'LIKE',
                        'queue' : 'LIKE',
                        'status' : 'EQ'
                    },
                    value: {
                        'name' : '',
                        'queue' : '',
                        'status' : ''
                    },
                },
                mounted: function(){
                    this.getJobList();
                },
                filters: {
                    /**
                     * 时间格式化
                     * @param val
                     * @returns {string}
                     * @constructor
                     */
                    DateFormat: function(val){
                        val = parseInt(val);
                        if(val === 0){
                            return '';
                        }
                        var date = new Date(parseInt(val));
                        return date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds()+'.'+date.getMilliseconds();
                    },
                    /**
                     * 返回一个大于等于零的数字
                     *
                     * @return {number}
                     */
                    HandleNumber: function(val){
                        val = parseInt(val);
                        if(val < 0 ){
                            return 0;
                        }
                        return val;

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
                    //任务重新入列页面
                    repush: function(job_id){
                        var url = "{:U('Queue/Index/repush')}&job_id=" + job_id;
                        layer.open({
                            type: 2,
                            title: '提示',
                            shadeClose: true,
                            shade: false,
                            maxmin: false, //开启最大化最小化按钮
                            area: ['600px', '400px'],
                            content: url
                        });
                    }
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