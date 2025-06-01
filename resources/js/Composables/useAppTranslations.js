import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue'; // ถ้าต้องการให้ translations หรือ locale เป็น computed ref

export function useAppTranslations() {
    const page = usePage();

    // page.props.translations และ page.props.locale จะ reactive
    // หมายความว่าถ้าค่าเหล่านี้เปลี่ยน (เช่น หลังจากการเปลี่ยนภาษา)
    // computed refs เหล่านี้ก็จะอัปเดตตามไปด้วย
    const translations = computed(() => page.props.translations || {});
    const currentLocale = computed(() => page.props.locale || 'en'); // ตั้งค่า default fallback

    function t(key, replacements = {}) {
        // ดึงค่าจาก translations ที่เป็น computed ref (ต้องใช้ .value)
        let translation = translations.value[key] || key; // ถ้าไม่พบคีย์ ให้ใช้คีย์นั้นเป็นค่า default

        // ส่วนการแทนที่ :placeholder ด้วยค่าจริง
        Object.keys(replacements).forEach(rKey => {
            translation = translation.replace(`:${rKey}`, replacements[rKey]);
        });
        return translation;
    }

    return {
        t, // ฟังก์ชันสำหรับแปลภาษา
        currentLocale // ref ของภาษาปัจจุบัน (ถ้าต้องการใช้)
    };
}
