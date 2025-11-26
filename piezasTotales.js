const cantPiezasSueltas = document.getElementById('cantidad-piezas-sueltas')
const cantidadCajas = document.getElementById('cantidad-cajas')
const piezasPorCaja = document.getElementById('piezas-por-caja')
const totalPiezas = document.getElementById('total-piezas')

const campos = [cantPiezasSueltas, cantidadCajas, piezasPorCaja]

campos.forEach((campo) => {
  campo.addEventListener('change', () => {
    const total =
      parseInt(cantPiezasSueltas.value) +
      parseInt(cantidadCajas.value) * parseInt(piezasPorCaja.value)
    totalPiezas.value = total
  })
})
