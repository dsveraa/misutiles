import { fileSizeVal } from './validaciones.js'

document.getElementById('imagen').addEventListener('change', (event) => {
  const file = event.target.files[0]
  if (!file) return

  const fileSizeValidation = fileSizeVal(file)
  
  if (!fileSizeValidation) {
    event.target.value = ''
    return
  }

  const preview = document.getElementById('img_preview')
  preview.src = URL.createObjectURL(file)
  preview.style.display = 'block'
  preview.classList.remove('subida')
  preview.classList.add('mostrada')
  document.getElementById('checkmark').style.display = 'none'
  document.getElementById('subir').style.display = 'block'
})

document.getElementById('subir').addEventListener('click', async () => {
  const file = document.getElementById('imagen').files[0]
  if (!file) {
    alert('Selecciona una imagen')
    return
  }

  const formData = new FormData()
  formData.append('image', file)

  const res = await fetch(
    'https://api.imgbb.com/1/upload?key=f16b4eb0e4d55fade31f25ecc307d809',
    {
      method: 'POST',
      body: formData,
    }
  )

  const data = await res.json()

  const url = data.data.url
  const preview = document.getElementById('img_preview')
  preview.classList.add('subida')
  const boton = document.getElementById('subir')
  boton.disabled = true
  boton.textContent = 'Subida'

  document.getElementById('imagen_url').value = url
  document.getElementById('checkmark').style.display = 'flex'
})
