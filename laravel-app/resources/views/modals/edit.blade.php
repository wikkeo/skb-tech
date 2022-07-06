<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование категории</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" name="edit" v-on:submit.prevent="update">
                <div>
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <input required type="text" class="form-control" name="name" placeholder="Название">
                        </div>
                        <div class="form-group">
                            <input required type="text" class="form-control" name="slug" placeholder="Уникальное название на англ.">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="description" placeholder="Описание"></textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="active" class="form-check-input">
                            <label class="form-check-label" for="exampleCheck1">Активность</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button name="submit" type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>