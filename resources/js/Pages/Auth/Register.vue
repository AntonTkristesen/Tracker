<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import TextInput from '../Components/TextInput.vue';


const form = useForm({
    avatar: null,
    name: null, 
    email: null,
    password: null,
    password_confirmation: null,
});

const change = (e) => {
    form.avatar = e.target.files[0];
};

const submit = () => {
    form.post('/register', {
        onError: () => form.reset('password', 'password_confirmation'),
    })
    console.log("test");
};
</script>

<template>
    <Head :title="` | ${$page.component}`" />
    <h1 class="title">Register</h1>

    <div class="container">
        <div class="row">
            <form @submit.prevent="submit" class="col-6">
                <div>
                    <label for="avatar">Avatar</label>
                    <input type="file" id="avatar" name="avatar" @input="change" />

                    <p>{{ form.errors.avatar }}</p>
                </div>

                <TextInput name="name" v-model="form.name" :message="form.errors.name"/>  

                <TextInput name="email" v-model="form.email" type="email" :message="form.errors.email"/>  

                <TextInput name="password" v-model="form.password" type="password" :message="form.errors.password"/>  
 
                <TextInput name="confirm password" type="password" v-model="form.password_confirmation"/>  
                <div>
                    <p>Already a user<a href="#">Login</a></p>
                    <Button type="submit" class="btn btn-primary">Register</Button>
                </div>
            </form>
        </div>
    </div>
</template>