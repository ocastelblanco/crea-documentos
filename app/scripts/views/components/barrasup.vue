<template>
  <div class="barrasup">
    <img class="logo" :src="logo" />
    <h3 class="titulo">Generador de documentos</h3>
    <ui-tab-bar class="barraTab" v-model="tabActiva">
      <ui-tab icon="edit" type="textWithIcon">Crear documento</ui-tab>
      <ui-tab icon="description" type="textWithIcon">
        Gestionar contenidos
      </ui-tab>
      <ui-tab icon="settings" type="textWithIcon">
        Gestionar plantillas
      </ui-tab>
    </ui-tab-bar>
    <span class="spacer"></span>
    <ui-button
      v-if="vinculo"
      class="boton-descarga"
      raised
      @click="descarga"
      :class="toolbarItemClass"
      icon="file_download"
    >
      Descargar
    </ui-button>
  </div>
</template>
<script>
import axios from 'axios';
import logo from '../../../assets/logo.png';

export default {
  name: 'barrasup',
  data() {
    return {
      logo: logo
    };
  },
  props: ['vinculo', 'nombreArchivo', 'tabActiva'],
  watch: {
    tabActiva: {
      handler(val) {
        this.cambiaTab();
      },
      deep: true
    }
  },
  emits: ['descargado', 'cambiaTab'],
  methods: {
    cambiaTab() {
      this.$emit('cambiaTab', this.tabActiva);
    },
    descarga() {
      axios.get(this.vinculo, { responseType: 'blob' }).then((response) => {
        const blob = new Blob([response.data], {
          type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = this.nombreArchivo;
        link.click();
        URL.revokeObjectURL(link.href);
        this.$emit('descargado');
      });
    }
  }
};
</script>