//peso imagen
export function fileSizeVal(file) {
  const MAX_SIZE = 2 * 1024 * 1024
  
  if (file.size > MAX_SIZE) {
      alert('La imagen pesa más de 2MB')
      return false
    }
  return true
}


//numero de estante
const form = document.getElementById("form")

form.addEventListener('submit', (e) => {
  const numeroEstante = document.getElementById("numero-estante")
  const valor = numeroEstante.value.trim()
  const n = parseInt(valor, 10)

  if (!/^\d{3}$/.test(valor) || n < 1 || n > 999) {
    e.preventDefault()
    alert('Número de estante debe ser entre 001 y 999')
  }
})

