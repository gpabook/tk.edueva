<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { watch, ref, onMounted, nextTick } from 'vue'; // เพิ่ม ref, onMounted, nextTick

const props = defineProps({
    teachers: Array,
    errors: Object,
    success: String,
    warning: String,
    error: String
});

const form = useForm({
    teachers: props.teachers.map(teacher => ({ id: teacher.id, name: teacher.name, email: teacher.email }))
});

watch(() => props.teachers, (newTeachers) => {
    form.teachers = newTeachers.map(teacher => ({ id: teacher.id, name: teacher.name, email: teacher.email }));
}, { deep: true });

// สร้าง refs สำหรับ input fields (สำหรับชื่อ)
// เราจะใช้ array ของ refs เพราะเรามี input หลายอัน
const nameInputRefs = ref([]);

// ฟังก์ชันสำหรับกำหนด ref ให้กับแต่ละ input
const setNameInputRef = (el) => {
    if (el) {
        nameInputRefs.value.push(el);
    }
};

// ก่อน component unmount ให้เคลียร์ refs
import { onBeforeUpdate, onUnmounted } from 'vue';
onBeforeUpdate(() => {
    nameInputRefs.value = []; // เคลียร์ก่อน re-render ทุกครั้งเพื่อป้องกัน refs ซ้ำซ้อน
});
onUnmounted(() => {
    nameInputRefs.value = [];
});


const submitForm = () => {
    const dataToSubmit = {
        teachers: form.teachers.map(teacher => ({ id: teacher.id, name: teacher.name }))
    };
    form.transform(() => dataToSubmit)
        .put(route('teachers.bulk-update.submit'), {
        preserveScroll: true,
    });
};

const handleKeyDown = (event, currentIndex) => {
    let newIndex = -1;

    if (event.key === 'ArrowDown') {
        event.preventDefault(); // ป้องกันการ scroll หน้าจอ
        if (currentIndex < nameInputRefs.value.length - 1) {
            newIndex = currentIndex + 1;
        }
    } else if (event.key === 'ArrowUp') {
        event.preventDefault(); // ป้องกันการ scroll หน้าจอ
        if (currentIndex > 0) {
            newIndex = currentIndex - 1;
        }
    }

    if (newIndex !== -1 && nameInputRefs.value[newIndex]) {
        // ใช้ nextTick เพื่อให้แน่ใจว่า DOM อัปเดตแล้ว (ถ้ามีการเปลี่ยนแปลง) ก่อนที่จะ focus
        nextTick(() => {
            nameInputRefs.value[newIndex].focus();
            nameInputRefs.value[newIndex].select(); // (Optional) เลือก text ทั้งหมดใน input ที่ focus ใหม่
        });
    }
};

</script>

<template>
    <Head :title="$t('bulk_edit_teachers_title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ $t('bulk_edit_teachers_header') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <form @submit.prevent="submitForm">
                    <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">ID</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Email</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Name (Editable)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="(teacher, index) in form.teachers" :key="teacher.id">
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">{{ teacher.id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">{{ teacher.email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        <TextInput
                                            type="text"
                                            class="block w-full mt-1"
                                            v-model="teacher.name"
                                            required
                                            :ref="setNameInputRef" @keydown.down="handleKeyDown($event, index)"
                                            @keydown.up="handleKeyDown($event, index)"
                                        />
                                    </td>
                                </tr>
                                <tr v-if="!form.teachers || form.teachers.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        No teachers found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ $t('save_all_changes_button') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
