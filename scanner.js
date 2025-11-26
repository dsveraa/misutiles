function cerrarModal() {
    const modal = document.getElementById('cameraModal')
    modal.style.display = 'none'

    if (scanner) {
        scanner.stop()
        scanner = null
    }

    if (stream) {
        stream.getTracks().forEach(t => t.stop())
        stream = null
    }

    codeReader.reset()
}

document.getElementById('closeCamera').addEventListener('click', cerrarModal)

//zxing
const codeReader = new ZXing.BrowserMultiFormatReader()
let stream = null

document.getElementById('scan').addEventListener('click', async () => {
    const modal = document.getElementById('cameraModal')
    const video = document.getElementById('videoBarcode')
    modal.style.display = 'flex'
    document.getElementById('videoBarcode').style.display = "block"
    document.getElementById('qrPreview').style.display = "none"

    const devices = await codeReader.getVideoInputDevices()

    stream = await navigator.mediaDevices.getUserMedia({
        video: { deviceId: devices[0].deviceId }
    })

    codeReader.decodeFromVideoDevice(
        devices[0].deviceId,
        video,
        (result, err) => {
            if (result) {
                document.getElementById('barcode').value = result.text
                cerrarModal()
                const boton = document.getElementById('scan')
                boton.disabled = true
                boton.textContent = 'Escaneado'
            }
        }
    )
})


//html5qrcode
let scanner = null

document.getElementById('scanBtn').addEventListener('click', () => {
    const modal = document.getElementById('cameraModal')
    modal.style.display = 'flex'
    document.getElementById('videoBarcode').style.display = "none"
    document.getElementById('qrPreview').style.display = "block"
    scanner = new Html5Qrcode("qrPreview")

    scanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (qrText) => {
            document.getElementById("codigo_qr").value = qrText
            cerrarModal()
            const boton = document.getElementById('scanBtn')
            boton.disabled = true
            boton.textContent = 'Escaneado'
        }
    )
})
