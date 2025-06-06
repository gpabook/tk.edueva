<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue'; // เพิ่มถ้ายังไม่มี
import { Head, useForm, Link } from '@inertiajs/vue3'; // เพิ่ม Link ถ้าจะใช้
import { ref, computed } from 'vue'; // เพิ่ม computed ถ้าจะใช้

// Props ที่รับมาจาก Controller ผ่าน Inertia
const props = defineProps({
    errors: Object, // Validation errors สำหรับฟอร์ม (เช่น ถ้าไม่ได้เลือกไฟล์)
    successMessage: String, // ข้อความแจ้งผลสำเร็จ
    importErrors: Array, // รายการ error/warning จากการประมวลผลแต่ละแถว
});

const csvFile = ref(null);

// useForm ของ Inertia สำหรับจัดการฟอร์มและไฟล์อัปโหลด
const form = useForm({
    csv_file: null,
});

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        csvFile.value = file;
        form.csv_file = file; // กำหนดไฟล์ให้กับ form object ของ Inertia
    } else {
        csvFile.value = null;
        form.csv_file = null;
    }
};

const submitImport = () => {
    if (!form.csv_file) {
        // อาจจะใช้ $t ถ้ามีการแปลข้อความนี้
        alert('Please select a CSV file to import.');
        return;
    }
    form.post(route('users.import.store'), { // route สำหรับการ import
        preserveScroll: true,
        onSuccess: () => {
            form.reset(); // รีเซ็ตฟอร์มของ Inertia
            csvFile.value = null; // รีเซ็ต ref ของไฟล์
            // เคลียร์ค่าของ input file element โดยตรง
            const fileInput = document.getElementById('csv_file_input');
            if (fileInput) {
                fileInput.value = null;
            }
        },
        onError: (pageErrors) => {
            // errors ที่เกี่ยวกับฟอร์มโดยรวม (เช่น validation ของ csv_file)
            // จะถูก handle โดย props.errors โดยอัตโนมัติจาก Inertia
            // ไม่จำเป็นต้องทำอะไรพิเศษที่นี่ เว้นแต่ต้องการ logic เพิ่มเติม
        }
    });
};

// Computed property สำหรับแยกประเภท error (ถ้าต้องการแสดงผลแยกกัน)
// ตัวอย่างนี้จะแสดง error ทั้งหมดรวมกันตามที่ Controller ส่งมาใน importErrors
const formattedImportErrors = computed(() => {
    if (!props.importErrors) {
        return [];
    }
    return props.importErrors.map(error => {
        let valuesDisplay = error.values;
        if (Array.isArray(error.values)) {
            valuesDisplay = error.values.join(' | ');
        }
        return {
            row: error.row || 'N/A', // แถวที่เกิดปัญหา (ถ้ามี)
            message: error.message || 'Unknown error', // ข้อความ error
            values: valuesDisplay || 'N/A', // ข้อมูลในแถวนั้น (ถ้ามี)
        };
    });
});

</script>

<template>
    <Head :title="$t('import_users_title')" /> <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ $t('import_users_header') }} </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="props.successMessage && !form.recentlySuccessful" class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded dark:border-green-600 dark:bg-green-700/30 dark:text-green-300">
                            {{ props.successMessage }}
                        </div>

                        <div v-if="formattedImportErrors.length > 0 && !form.recentlySuccessful" class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded dark:border-red-600 dark:bg-red-700/30 dark:text-red-300">
                            <h3 class="mb-2 font-bold">{{ $t('import_processing_errors_title') }}</h3> <ul class="space-y-1 overflow-y-auto text-sm list-disc max-h-60 ps-5">
                                <li v-for="(error, index) in formattedImportErrors" :key="index">
                                    <span class="font-semibold">{{ $t('import_error_row') }} {{ error.row }}:</span> {{ error.message }}
                                    <span v-if="error.values && error.values !== 'N/A'" class="block text-xs text-gray-600 dark:text-gray-400">({{ $t('data_values') }}: {{ error.values }})</span> </li>
                            </ul>
                        </div>

                        <div v-if="props.errors && Object.keys(props.errors).length > 0 && !form.recentlySuccessful" class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded dark:border-red-600 dark:bg-red-700/30 dark:text-red-300">
                             <h3 class="mb-2 font-bold">{{ $t('form_validation_errors_title') }}</h3> <ul class="space-y-1 text-sm list-disc ps-5">
                                <li v-for="(errorMsg, field) in props.errors" :key="field">
                                    {{ errorMsg }}
                                </li>
                             </ul>
                        </div>


                        <form @submit.prevent="submitImport" class="space-y-6">
                            <div>
                                <InputLabel for="csv_file_input" :value="$t('select_csv_file_label')" class="mb-1" />
                                <input
                                    type="file"
                                    id="csv_file_input"
                                    @change="handleFileUpload"
                                    accept=".csv, text/csv"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:placeholder-gray-400"
                                    :disabled="form.processing"
                                />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" id="file_input_help">
                                    {{ $t('csv_file_format_note') }} </p>
                                </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing || !form.csv_file">
                                    {{ form.processing ? $t('processing_button') : $t('import_button') }} </PrimaryButton>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $t('import_submitted_message') }} </p>
                                </Transition>
                            </div>
                        </form>

                        <div class="pt-6 mt-8 border-t dark:border-gray-700">
                            <h4 class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300">{{ $t('csv_format_guidelines_title') }}</h4>
                            <p class="mb-1 text-sm text-gray-600 dark:text-gray-400">{{ $t('csv_ensure_header_row') }}</p>
                            <ul class="space-y-1 text-sm text-gray-600 list-disc ps-5 dark:text-gray-400">
                                <li>{{ $t('csv_column_student_id') }} <code class="px-1 text-xs bg-gray-200 rounded dark:bg-gray-600">student_id</code> ({{ $t('required_field') }})</li>
                                <li>{{ $t('csv_column_name_th') }} <code class="px-1 text-xs bg-gray-200 rounded dark:bg-gray-600">name_th</code> ({{ $t('required_field') }})</li>
                                <li>{{ $t('csv_column_surname_th') }} <code class="px-1 text-xs bg-gray-200 rounded dark:bg-gray-600">surname_th</code> ({{ $t('required_field') }})</li>
                                <li>{{ $t('csv_column_email') }} <code class="px-1 text-xs bg-gray-200 rounded dark:bg-gray-600">email</code> ({{ $t('required_field') }}, {{ $t('unique_field') }})</li>
                                <li>{{ $t('csv_column_role') }} <code class="px-1 text-xs bg-gray-200 rounded dark:bg-gray-600">role</code> ({{ $t('optional_field') }}, {{ $t('default_role_note', {defaultRole: 'student'}) }})</li>
                                </ul>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $t('csv_example_download') }}
                                <a href="/examples/user_import_template.csv" download class="text-blue-600 hover:underline dark:text-blue-400">{{ $t('download_template_link') }}</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
            </div>
        </AuthenticatedLayout>
    </template>
