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
            alreadyCropped: true
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
    const fullName = result.text?.getField(SURNAME_AND_GIVEN_NAMES)?.value;
    const docNumber = result.text?.getField(DOCUMENT_NUMBER)?.value;
    const birthDate = result.text?.getField(DATE_OF_BIRTH)?.value;

    alert(`Name: ${fullName}\nPassport: ${docNumber}`);

    // 📤 send to Laravel
};



