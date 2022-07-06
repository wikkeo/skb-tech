new Vue({
    el: '#app',
    data: {
        baseUrl: '/',
        categories: [],
        page: 1,
        pageSize: 2, // по-умолчанию 2
        pages: [],
        currentID: 0,
        sortData: [],
        show_filter: true,
        filterData: [],
        search_string: ''
    },
    methods: {
        getList: function() {
            let data = {
                'sort': this.sortData,
                'filter': this.filterData,
                'search': this.search_string
            }
            axios.post(this.baseUrl + 'list', data)
                .then(response => {
                    this.categories = response.data;
                })
                .catch(response => {
                    toastr.error(response);
                });
        },
        setModalData: function(id) {
            this.setCurrent(id);
            let editRow = this.get(id);
            for (field in editRow) {
                let input = document.querySelector(`form[name="edit"] *[name="${field}"]`);
                if (input && (input.type == 'text' || input.type == 'hidden')) {
                    input.value = editRow[field];
                } else if (input && input.type == 'checkbox') {
                    input.checked = !!editRow[field];
                } else if (input && input.type == 'textarea') {
                    input.value = editRow[field];
                }
            }
        },
        setCurrent: function(id) {
            this.currentID = id;
        },
        get: function(id) {
            for (let key in this.categories) {
                if (this.categories[key].id == id) {
                    return this.categories[key];
                }
            }
        },
        append: function(event) {
            let prepared = new FormData(event.target);
            let active_field = document.querySelector('form[name="append"] input[name="active"]');
            prepared.set('active', active_field.checked ? 1 : 0);
            this.api('create', '#appendModal', prepared, function() {
                toastr.success('Категория создана!');
                Array.from(document.querySelectorAll('form[name="append"] *[data-append]'), function(item) {
                    if (item.type == 'text' || item.type == 'textarea') {
                        item.value = '';
                    } else if (item.type == 'checkbox') {
                        item.checked = false;
                    }
                });
            });
        },
        drop: function() {
            let prepared = new FormData();
            prepared.append('id', this.currentRow);
            this.api('delete', '#deleteModal', prepared, function() {
                toastr.success('Категория удалена!');
            });
        },
        update: function(event) {
            let prepared = new FormData();
            let editRow = this.get(this.currentID);
            let fields = [];
            for (field in editRow) {
                let input = document.querySelector(`form[name="edit"] *[name="${field}"]`);
                if (input && (input.type == 'text' || input.type == 'hidden' || input.type == 'textarea')) {
                    if (input.value != editRow[field]) {
                        fields.push(field);
                    }
                } else if (input && input.type == 'checkbox') {
                    if (+input.checked !== editRow[field]) {
                        fields.push(field);
                    }
                }
            }
            if (!fields.length) {
                toastr.error('Поля не были изменены!');
                return;
            }
            Array.from(fields, function(field) {
                let input = document.querySelector(`form[name="edit"] *[name="${field}"]`);
                prepared.append(field, (
                    (
                        input.type == 'text' ||
                        input.type == 'hidden' ||
                        input.type == 'textarea'
                    ) ? input.value.trim() : +input.checked));
            });
            prepared.append('id', this.currentID);
            this.api('update', '#editModal', prepared, function() {
                toastr.success('Категория отредактирована!');
            });
        },
        filter_state: function() {
            this.show_filter = (this.show_filter == false ? true : false);
            return this.show_filter;
        },
        filter: function() {
            // игнорируем фильтрацию при наличии строки поиска
            if (this.search_string.trim().length > 0) {
                return;
            }
            // берем элементы фильтра
            let inputs = document.querySelectorAll(`#filter *[data-filter]`);
            // собираем
            let filter_data = [];
            Array.from(inputs, function(item) {
                let field_name = item.getAttribute('name');
                if (item.type == 'text' && item.value.trim().length > 0) {
                    filter_data.push({
                        'name': field_name,
                        'value': item.value
                    });
                } else if (item.type == 'select-one' && item.value != -1) {
                    filter_data.push({
                        'name': field_name,
                        'value': item.value
                    });
                }
            });
            this.filterData = filter_data;
            this.getList();
        },
        search: function() {
            let search_string = document.getElementById('search_string');
            this.search_string = search_string.value.trim();
            if (this.search_string.length > 0) {
                this.getList();
            }
        },
        search_clear: function() {
            let search_string = document.getElementById('search_string');
            search_string.value = this.search_string = '';
            this.getList();
        },
        search_keypress: function(event) {
            if (event.which === 13) {
                this.search();
            }
        },
        filter_clear: function() {
            let inputs = document.querySelectorAll(`#filter *[data-filter]`);
            Array.from(inputs, function(item) {
                if (item.type == 'text') {
                    item.value = '';
                } else if (item.type == 'select-one') {
                    item.value = -1;
                }
            });
            this.filterData = [];
            this.getList();
        },
        /* блок пагинации */
        setPages() {
            this.pages = [];
            let numberOfPages = Math.ceil(this.categories.length / this.pageSize);
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        },
        paginate(categories) {
            let page = this.page;
            let pageSize = this.pageSize;
            let from = (page * pageSize) - pageSize;
            let to = (page * pageSize);
            return categories.slice(from, to);
        },
        sort: function(field) {
            let sortData = [];
            let input = document.querySelector(`#header th[name="${field}"]`);
            let order = input.getAttribute('aria-sort');
            input.setAttribute('aria-sort', (order == 'descending' ? 'ascending' : 'descending'));
            Array.from(document.querySelectorAll(`#header th[aria-sort]`), function(item) {
                sortData.push(
                    (
                        (item.getAttribute('aria-sort') == 'descending' ? '-' : '+') + item.getAttribute('name')
                    )
                )
            });
            // сделать порядок сортировки
            this.sortData = sortData;
            this.getList();
        },
        /* api вызовы */
        api: function(methodType, control, data, callback) {
            axios.post(this.baseUrl + methodType, data)
                .then(response => {
                    $(control).modal('hide');
                    this.getList();
                    callback && callback();
                })
                .catch(response => {
                    toastr.error(response);
                });
        }
    },
    created: function() {
        this.getList();
    },
    watch: {
        categories() {
            this.setPages();
        }
    },
    computed: {
        paginatedCategories: function() {
            return this.paginate(this.categories);
        },
        currentRow: function() {
            return this.currentID;
        }
    }
});