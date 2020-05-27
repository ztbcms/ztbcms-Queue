<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <h3>异常任务列表</h3>
            <div class="filter-container">
                <el-input v-model="listQuery.search_name" placeholder="任务名称" style="width: 200px;"
                          class="filter-item"></el-input>
                <el-button class="filter-item" type="primary" style="margin-left: 10px;"
                           @click="getList">
                    筛选
                </el-button>
            </div>

            <el-table
                :key="tableKey"
                :data="list"
                border
                fit
                highlight-current-row
                style="width: 100%;"
            >
                <el-table-column label="ID" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.id }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="任务名称" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.name }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="队列" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.queue}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="数据" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.payload}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="重试次数" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.attempts}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="创建时间" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.create_time | DateFormat }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="可用时间" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.available_at | DateFormat  }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="出队时间" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.reserved_at | DateFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="开始时间" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.start_time | DateFormat }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="结束时间" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.end_time | DateFormat }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="耗时" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.end_time -  scope.row.start_time | HandleNumber}} ms</span>
                    </template>
                </el-table-column>

                <el-table-column label="失败原因" align="center">
                    <template slot-scope="scope">
                        <span style="overflow: hidden;text-overflow:ellipsis;white-space: nowrap;">
                            {{ scope.row.exception}}
                        </span>
                        <el-link type="primary" @click="openDetail(scope.row.exception)">详情</el-link>
                    </template>
                </el-table-column>

                <el-table-column label="操作" align="center" class-name="small-padding fixed-width" >
                    <template slot-scope="scope">
                        <el-button size="mini" type="danger"
                                   @click="deleteJob(scope.row.id)">
                            删除
                        </el-button>
                    </template>
                </el-table-column>

            </el-table>

            <div class="pagination-container">
                <el-pagination
                    background
                    layout="prev, pager, next, jumper"
                    :total="listQuery.total"
                    v-show="listQuery.total>0"
                    :current-page.sync="listQuery.page"
                    :page-size.sync="listQuery.limit"
                    @current-change="getList"
                >
                </el-pagination>
            </div>

        </el-card>

        <el-dialog
                title="失败原因"
                :visible.sync="dialogVisible"
                width="30%">
            <span>{{showMsg}}</span>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
            </span>
        </el-dialog>
    </div>

    <style>
        .filter-container {
            padding-bottom: 10px;
        }

        .pagination-container {
            padding: 32px 16px;
        }
    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    dialogVisible: false,
                    showMsg: '',
                    tableKey: 0,
                    list: [],
                    listQuery: {
                        page: 1,
                        limit: 15,
                        total: 0
                    },
                },
                watch: {},
                filters: {
                    /**
                     * 时间格式化
                     */
                    parseTime: function (time, format) {
                        return Ztbcms.formatTime(time, format)
                    },

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
                    //获取任务列表数据
                    getList(){
                        var that = this;
                        $.ajax({
                            url:"{:U('Queue/Manage/getFaildJobList')}",
                            dataType:"json",
                            data: that.listQuery,
                            type:"get",
                            success(res){
                                console.log(res)
                                if(res.status){
                                    that.list = res.data.items;
                                    that.listQuery.total = res.data.amount;
                                    that.listQuery.limit = res.data.limit;
                                    that.listQuery.page = res.data.page;
                                }
                            }
                        })
                    },
                    //删除任务
                    deleteJob: function (job_id){
                        var that = this;
                        layer.confirm('确认删除?', function(index){
                            that.doDeleteJob(job_id)
                            layer.close(index);
                        });
                    },
                    //删除任务
                    doDeleteJob: function (job_id) {
                        var that = this;
                        var data = {
                            job_id: job_id,
                        };
                        $.ajax({
                            url: "{:U('Queue/Manage/doDeleteFaildJob')}",
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            success: function (res) {
                                layer.msg(res.msg);
                                that.getList();
                            }
                        });
                    },
                    //打开详情
                    openDetail(msg) {
                        this.dialogVisible = true;
                        this.showMsg = msg;
                    }
                },
                mounted: function () {
                    this.getList();
                },

            })
        })
    </script>
</block>