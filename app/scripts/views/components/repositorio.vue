<template>
  <div class="repositorio">
    <ui-grid class="grid">
      <ui-grid-cell columns="6">
        <div class="tarjeta">
          <h3>
            Guardar el contenido actual
            <small>con {{ plantillaActiva.nombre }}</small>
          </h3>
          <ui-textfield v-model="descripcion" outlined fullwidth>
            Descripción del contenido
          </ui-textfield>
          <div class="acciones">
            <ui-button raised icon="save" @click="guardaDocumento">
              Guardar contenido
            </ui-button>
          </div>
        </div>
      </ui-grid-cell>
      <ui-grid-cell columns="6" v-if="lista.length > 0">
        <div class="tarjeta">
          <h3>Cargar o eliminar un contenido guardado anteriormente</h3>
          <ui-select fullwidth :options="lista" v-model="documentoActivo">
            Documentos guardados
          </ui-select>
          <div class="acciones">
            <ui-button raised icon="input" @click="cargaDocumento">
              Cargar
            </ui-button>
            <ui-button outlined icon="delete" @click="borraDocumento">
              Eliminar
            </ui-button>
          </div>
        </div>
      </ui-grid-cell>
    </ui-grid>
  </div>
</template>

<script>
export default {
  name: 'repositorio',
  data() {
    return {
      lista: [],
      descripcion: ''
    };
  },
  props: ['listaDocumentos', 'documentoActivo', 'plantillaActiva'],
  mounted() {
    this.formaLista(this.listaDocumentos);
  },
  emits: ['cargaDocumento', 'borraDocumento', 'guardaDocumento'],
  methods: {
    guardaDocumento() {
      this.$emit('guardaDocumento', this.descripcion);
      this.descripcion = '';
    },
    cargaDocumento() {
      this.$emit('cargaDocumento', this.documentoActivo);
    },
    borraDocumento() {
      this.$confirm({
        message: '¿Eliminar el contenido seleccionado?',
        acceptText: 'Eliminar',
        cancelText: 'Cancelar'
      }).then((res) => {
        if (res) {
          this.$emit('borraDocumento', this.documentoActivo);
        }
      });
    },
    formaLista(val) {
      this.lista = [];
      if (val && val.length > 0 && val[0] !== null) {
        val.forEach((doc, ind) => {
          if (doc.plantilla === this.plantillaActiva.nombre) {
            this.lista.push({ label: doc.nombre, value: ind });
          }
        });
      }
    }
  },
  watch: {
    listaDocumentos: {
      handler(val) {
        this.formaLista(val);
      },
      deep: true
    },
    plantillaActiva: {
      handler(val) {
        this.formaLista(this.listaDocumentos);
      },
      deep: true
    }
  }
};
</script>