<template>
  <div class="contenido">
    <div class="encabezado">
      <h2>Vista previa</h2>
      <table>
        <tr>
          <th>Nombre:</th>
          <td>{{ plantilla.nombre }}</td>
        </tr>
        <tr>
          <th>Descripción:</th>
          <td>{{ plantilla.descripcion }}</td>
        </tr>
      </table>
    </div>
    <div class="prevista" v-html="salida"></div>
  </div>
</template>
<script>
export default {
  name: 'contenido',
  data() {
    return {
      salida: ''
    };
  },
  props: ['form', 'textos', 'plantilla'],
  watch: {
    form: {
      handler(val) {
        this.creaSalida();
      },
      deep: true
    }
  },
  methods: {
    creaSalida() {
      this.salida = '';
      this.textos.forEach((texto, index) => {
        this.salida += texto;
        if (index < this.form.length) {
          let valor;
          if (this.form[index].nombre) {
            valor = this.form[index].valor;
          } else {
            const n = parseInt(this.form[index].id.substr(2));
            valor = this.form[n].valor;
          }
          const variable = valor.length > 0 ? valor : '_'.repeat(12);
          this.salida += '<span class="variable">' + variable + '</span>';
        }
      });
    }
  },
  mounted() {
    window.setTimeout(() => {
      this.creaSalida();
    }, 1000);
  }
};
</script>