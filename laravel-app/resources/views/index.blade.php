<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Тестовое приложение для Совкомбанк-технологии</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/app.css')}}">
</head>
<body>
	<div class="container" id="app">
		<div class="row">
			<div class="col mt-1">
                <div class="clearfix service-block">
                    <div class="float-left show-count">
                        <label for="drop-page" class="float-left">На странице:</label>
                        <div class="dropdown float-left" id="drop-page">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@{{pageSize}}</button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @for ($i = 1; $i <= 10; $i++)
                                    <a class="dropdown-item" href="#" @click="pageSize = {{$i}}; setPages()">{{$i}}</a>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="float-right append-button">
                        <button class="btn btn-success mb-12" data-toggle="modal" data-target="#appendModal">Добавить</button>
                        <button class="btn btn-primary mb-12" @click="filter_state()"><i class="fa fa-filter"></i> Фильтр</button>
                    </div>
                </div>
                <div class="search-bar">
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" class="form-control" aria-label="Small" id="search_string" aria-describedby="search-button" @keyup="search_keypress">
                        <div class="input-group-prepend">
                            <button class="input-group-text" id="search-button" v-on:click="search()">Поиск</button>
                            <button class="input-group-text btn-danger" id="search-button-clear" v-on:click="search_clear()">Сброс</button>
                        </div>
                    </div>
                </div>
				<table class="table shadow b-table">
					<thead class="thead-light">
						<tr id="header">
							<th aria-sort="ascending" name="name" @click="sort('name')">Название категории</th>
                            <th aria-sort="ascending" name="slug" @click="sort('slug')">Англ. название</th>
							<th>Описание</th>
							<th aria-sort="ascending" name="createdDate" @click="sort('createdDate')">Дата создания</th>
                            <th>Активность</th>
                            <th>Операции</th>
						</tr>
                        <tr id="filter" :class="show_filter ? 'd-none' : ''">
							<td>
                                <div class="input-group-sm">
                                    <input type="text" name="name" data-filter class="form-control" @keyup="filter()">
                                </div>
                            </td>
                            <td>
                                <div class="input-group-sm">
                                    <input name="slug" type="text" data-filter class="form-control" @keyup="filter()">
                                </div>
                            </td>
							<td>
                                <div class="input-group-sm">
                                    <input type="text" name="description" data-filter class="form-control" @keyup="filter()">
                                </div>
                            </td>
							<td name="createdDate"></td>
                            <td>
                                <div class="input-group-sm">
                                    <select class="form-control" data-filter name="active" @change="filter()">
                                        <option value="-1"></option>
                                        <option value="1">Да</option>
                                        <option value="0">Нет</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="input-group-sm">    
                                    <button class="btn-danger" v-on:click="filter_clear()">Сброс</button>
                                </div>    
                            </td>
						</tr>
                        <tr v-for="(item, index) in paginatedCategories" v-cloak v-bind:key="item.id">
                            <td>@{{ item.name }}</td>
                            <td>@{{ item.slug }}</td>
							<td>@{{ item.description }}</td>
							<td>@{{ item.createdDate }}</td>
                            <td>@{{ item.active == 1 ? 'Да' : 'Нет' }}</td>
							<td>
								<a @click="setModalData(item.id)" class="btn btn-success btn-sm" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a> 
								<a @click="setCurrent(item.id)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
					</thead>
				</table>
			</div>
            <!-- пагинируем -->
            <div class="clearfix btn-group col-md-2 offset-md-5">
                <button type="button" class="btn btn-sm btn-outline-secondary" v-if="page != 1" @click="page--"> << </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" v-for="pageNumber in pages.slice(page-1, page+5)" @click="page = pageNumber"> @{{pageNumber}} </button>
                <button type="button" @click="page++" v-if="page < pages.length" class="btn btn-sm btn-outline-secondary"> >> </button>
            </div>
		</div>
        @include('modals.append')
        @include('modals.edit')
        @include('modals.delete')
	</div>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ URL::asset('js/app.js') }}"></script>
</body>
</html>