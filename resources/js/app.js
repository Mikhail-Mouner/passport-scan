import './bootstrap';
import { DocumentReaderApi, Scenario, TextFieldType, GraphicFieldType } from '@regulaforensics/document-reader-webclient';


// 🎯 fields
const {
    DOCUMENT_NUMBER,
    SURNAME_AND_GIVEN_NAMES,
    DATE_OF_BIRTH
} = TextFieldType;

// 🎯 init Regula
const api = new DocumentReaderApi({
    basePath: 'http://localhost:8080',
});

// 🎥 تشغيل الكاميرا
const video = document.getElementById("video");

async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
}

startCamera();

// 📸 scan
window.scan = async function () {

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    const base64 = canvas.toDataURL('image/jpeg').split(',')[1];

    const result = await api.process({
        processParam: {
            scenario: "FullAuth", // or Scenario.FullAuth if API accepts enum
            authParams: {
                checkLiveness: false
            },
            alreadyCropped: false
        },
        List: [
            {
                ImageData: {
                    image: base64
                },
                light: 6,
                page_idx: 0
            }
        ]
    });

    console.log(result);

    // ✅ extract data
    const getField = (field) => result.text?.getField(field)?.value ?? 'unknown';

    const fullName =
        getField(SURNAME_AND_GIVEN_NAMES) ||
        getField(TextFieldType.FULL_NAME) ||
        getField(TextFieldType.NAME);

    const docNumber = getField(DOCUMENT_NUMBER);
    const birthDate = getField(DATE_OF_BIRTH);

    alert(`Name: ${fullName}\nPassport: ${docNumber} \nDate of Birth: ${birthDate}`);

    // 📤 send to Laravel
};



