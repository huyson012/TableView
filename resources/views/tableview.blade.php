<{{ $htmlId }}></{{ $htmlId }}>

<script type="x-template" id="{{ $htmlId }}">
<div id="{{ $htmlId }}">
    <el-row type="flex" justify="end" style="margin-bottom: 15px;">
        <el-col :span="6">
            <el-input
                icon="search"
                placeholder="@lang('laravel-table-view::tableview.search')"
                v-model="search"
                @keyup.native.enter="handleSearch">
                <el-button slot="append" icon="search" @click="handleSearch">@lang('laravel-table-view::tableview.search')</el-button>
            </el-input>
        </el-col>
    </el-row>

    <el-row type="flex">
        <el-table
            :data="tableData"
            v-loading="isLoading"
            element-loading-text="@lang('laravel-table-view::tableview.loading')"

            {!! $attributes->render() !!}

            style="width: 100%"
            @sort-change="handleSortChange">

            <span slot="empty">@lang('laravel-table-view::tableview.empty')</span>

            @foreach($columns as $column)

                @include($column->getViewName(), ['column' => $column])

            @endforeach

        </el-table>
    </el-row>

    <el-row type="flex" justify="end" style="margin-top: 15px;">
        <el-pagination
          layout="prev, pager, next"
          @current-change="handlePageChange"
          :current-page="current_page"
          :page-size="page_size"
          :total="total_items">
        </el-pagination>
    </el-row>
</div>
</script>

@push('table-view-scripts')
<script type="text/javascript">
   Vue.component('{{ $htmlId }}', {

        template: '#{{ $htmlId }}',

        data() {
            return {
                tableData: [],
                isLoading: true,

                current_page: 0,
                page_size: 15,
                total_items: 0,

                sort: null,
                order: null,

                search: '',
            }
        },

        computed: {
            default_sort() {
                return {
                    prop: this.sort,
                    order: this.order
                }
            }
        },

        methods: {
            handlePageChange(page) {
                this.fetchData(page);
            },

            handleSearch() {
                this.fetchData();
            },

            handleSortChange(sorting) {
                this.sort = sorting.prop;
                this.order = sorting.order;

                this.fetchData();
            },

            fetchData(page) {
                this.isLoading = true;

                // Pagination
                this.page = page || this.page;

                formattedOrder = this.order == 'descending' ? 'desc' : 'asc';

                let params = {
                    page: this.page,
                    limit: this.page_size,
                    sort: this.sort,
                    order: formattedOrder,
                    search: this.search,
                }

                var self = this;
                this.getList(params).then(function (response) {
                    self.tableData = response.data.data;

                    self.current_page = response.data.meta.pagination.current_page;
                    self.total_items = response.data.meta.pagination.total;
                    self.page_size = response.data.meta.pagination.per_page;

                    self.isLoading = false;
                })
            },

            getList(params) {
                return axios.get('{{ $apiURL }}', {
                    params: params,
                })
            },

            @stack('table-view-methods')

        },

        mounted() {
            this.fetchData();
        },
    });
</script>
@endpush
