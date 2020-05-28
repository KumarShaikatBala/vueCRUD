<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users Table</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <button class="btn btn-success" @click="addModal()">Add New <i class="fa fa-user-plus"></i></button>
                            <!-- Modal -->
                            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form @submit.prevent="editMode? updateUser() :createUser()">
                                        <div class="modal-header">
                                            <h5 class="modal-title" v-show="!editMode">Add New</h5>
                                            <h5 class="modal-title" v-show="editMode">Edit User</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                                <div class="form-group">
                                                    <label>Username</label>
                                                    <input v-model="form.name" type="text" name="name"
                                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                                    <has-error :form="form" field="name"></has-error>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input v-model="form.email" type="text" name="email"
                                                           class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                                    <has-error :form="form" field="email"></has-error>
                                                </div>
                                                <div class="form-group">
                                                    <label>BIO</label>
                                                    <textarea v-model="form.bio" type="text" name="bio" placeholder="short Bio For User"
                                                           class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                                                    </textarea>
                                                    <has-error :form="form" field="bio"></has-error>
                                                </div>
                                            <div class="form-group">
                                                <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                                    <option value="">Select User Role</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="user">Standard User</option>
                                                    <option value="author">Author</option>
                                                </select>
                                                <has-error :form="form" field="type"></has-error>
                                            </div>

                                            <div class="form-group">
                                                    <label>Password</label>
                                                    <input v-model="form.password" type="password" name="password"
                                                           class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                                    <has-error :form="form" field="password"></has-error>
                                                </div>


                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
                                            <button v-show="editMode" type="submit" class="btn btn-secondary">Update</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Modify</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{user.id}}</td>
                            <td>{{user.name}}</td>
                            <td>{{user.email}}</td>
                            <td>{{user.type}}</td>
                            <td>{{user.created_at |myDate}}</td>
                            <td>
                                <a href="javascript:void(0)" @click="deleteUser(user.id)"><i class="fa fa-trash"></i></a>
                                <a href="javascript:void(0)" @click="editUser(user)"><i class="fa fa-edit"></i></a>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                editMode:false,

                // Create a new form instance
                users:{},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    bio: '',
                    type: '',
                    password: '',
                    remember: false
                })
            }
        },
        methods:{
            addModal(){
                this.form.reset();
                $('#addNew').modal('show');
                this.editMode=false;
            },
            loadUsers(){
                axios.get('api/user').then(({data})=>(this.users=data.data));
            },
            createUser(){
                this.$Progress.start();
                this.form.post('api/user')
                .then(()=>{
                    Fire.$emit('afterCreate');
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: 'Signed in successfully'
                    });

                })
                .catch(()=>{
                    this.$Progress.fail();
                })
                this.$Progress.finish();
            },
            editUser(user){
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
                this.editMode=true;
            },
            updateUser(){
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id)
                .then(()=>{
                    Fire.$emit('afterCreate');
                    $('#addNew').modal('hide');
                    Swal.fire(
                        'Updated!',
                        'Your file has been Updated.',
                        'success'
                    );
                })
                .catch(()=>{
                    this.$Progress.fail();
                });
                this.$Progress.finish();
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                    this.form.delete('api/user/'+id)
                        .then(()=>{
                            this.$Progress.start();
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        );
                        Fire.$emit('afterCreate');
                            this.$Progress.finish();
                    })
                        .catch(()=>{
                            this.$Progress.fail();
                        })
                    }
                })
            }

        },
        /*        created() {
                },*/
        mounted() {
            this.loadUsers();
            /*setInterval( ()=>this.loadUsers(),3000);*/
            Fire.$on('afterCreate',()=>{
                this.loadUsers();
            });

        }
    }
</script>
