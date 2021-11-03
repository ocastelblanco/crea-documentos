<template>
  <div class="app">
    <barrasup
      :vinculo="vinculo"
      :nombreArchivo="nombreArchivo"
      :tabActiva="tabActiva"
      @descargado="descargado"
      @cambiaTab="cambiaTab"
    >
    </barrasup>
    <ui-panels v-model="tabActiva">
      <ui-panel>
        <div class="contenedor">
          <ui-grid class="grid" v-if="listaPlantillas.length > 0">
            <ui-grid-cell class="celda formulario" columns="4">
              <formulario
                @crear="creaWord"
                @limpiar="limpiar"
                :form="form"
                :plantilla="plantillaActiva"
              ></formulario>
            </ui-grid-cell>
            <ui-grid-cell class="celda preview" columns="8">
              <contenido
                :form="form"
                :textos="textos"
                :plantilla="plantillaActiva"
              ></contenido>
            </ui-grid-cell>
          </ui-grid>
          <ui-alert v-else state="warning">
            <p>
              Es necesario cargar una plantilla para poder generar un documento.
            </p>
            <p>
              Abra la sección <strong>Gestionar plantillas</strong> para cargar
              una nueva
            </p>
          </ui-alert>
        </div>
      </ui-panel>
      <ui-panel>
        <repositorio
          :listaDocumentos="listaDocumentos"
          :documentoActivo="documentoActivo"
          :plantillaActiva="plantillaActiva"
          @guardaDocumento="guardaDocumento"
          @cargaDocumento="cargaDocumento"
          @borraDocumento="borraDocumento"
        >
        </repositorio>
      </ui-panel>
      <ui-panel>
        <gestiona
          :listaPlantillas="listaPlantillas"
          :numPlantilla="numPlantilla"
          :plantilla="plantillaActiva"
          @cambiaPlantilla="cambiaPlantilla"
          @cargaNuevaPlantilla="cargaNuevaPlantilla"
          @eliminarPlantilla="eliminarPlantilla"
        ></gestiona>
      </ui-panel>
    </ui-panels>
  </div>
</template>

<script>
import formulario from '@/views/components/formulario';
import barrasup from '@/views/components/barrasup';
import contenido from '@/views/components/contenido';
import gestiona from '@/views/components/gestiona';
import repositorio from '@/views/components/repositorio';
import axios from 'axios';
import mammoth from 'mammoth/mammoth.browser';

export default {
  name: 'App',
  components: {
    formulario,
    barrasup,
    contenido,
    gestiona,
    repositorio
  },
  data() {
    return {
      contenidoPlantilla: '',
      form: [],
      textos: [],
      vinculo: '',
      nombreArchivo: '',
      plantillaActiva: {},
      listaPlantillas: [],
      numPlantilla: 0,
      tabActiva: 0,
      listaDocumentos: [],
      documentoActivo: 0,
      //rutaAPI: 'http://localhost/aulaplaneta/api/plantilla-word/'
      rutaAPI: 'api/'
    };
  },
  mounted() {
    this.cargaListaPlantillas();
    this.cargaListaDocumentos();
  },
  methods: {
    guardaDocumento(nombre) {
      const salida = {
        plantilla: this.plantillaActiva.nombre,
        nombre: nombre,
        valores: this.form
      };
      const param = new FormData();
      param.append('documento', JSON.stringify(salida));
      axios
        .post(this.rutaAPI + 'data.php?accion=guarda', param)
        .then((resp) => {
          if (resp.data.error === null) {
            this.$alert('Contenido guardado con éxito');
            this.cargaListaDocumentos();
          } else {
            this.$alert('Error al guardar el contenido');
          }
        });
    },
    cargaDocumento(num) {
      const param = new FormData();
      param.append('id', num);
      axios.post(this.rutaAPI + 'data.php?accion=carga', param).then((resp) => {
        if (resp.data.error === null) {
          this.form = resp.data.valores;
        } else {
          this.$alert('Error al cargar el contenido');
        }
      });
    },
    borraDocumento(num) {
      const param = new FormData();
      param.append('id', num);
      axios.post(this.rutaAPI + 'data.php?accion=borra', param).then((resp) => {
        if (resp.data.error === null) {
          this.$alert('Contenido eliminado con éxito');
          this.cargaListaDocumentos();
        } else {
          this.$alert('Error al eliminar el contenido');
        }
      });
    },
    cargaListaDocumentos() {
      axios.get(this.rutaAPI + 'data.php?accion=lista').then((resp) => {
        if (resp.data.error === null) {
          this.listaDocumentos = resp.data.valores;
        } else {
          this.$alert('Error al cargar la lista de documentos');
        }
      });
      this.documentoActivo = 0;
    },
    eliminarPlantilla(num) {
      const param = new FormData();
      param.append('num', num);
      axios.post(this.rutaAPI + 'borra.php', param).then((res) => {
        if (res.data.exito) {
          if (this.numPlantilla > 0) this.numPlantilla--;
          this.cargaListaPlantillas();
        }
      });
    },
    cargaListaPlantillas() {
      axios
        .get(this.rutaAPI + 'plantillas/lista.json', { responseType: 'json' })
        .then((response) => {
          this.listaPlantillas = response.data;
          this.cargaPlantilla();
        });
    },
    cargaNuevaPlantilla(data) {
      const params = new FormData();
      params.append('file', data.archivo);
      params.append('datos', JSON.stringify(data.datos));
      axios
        .post(this.rutaAPI + 'nueva.php', params)
        .then((response) => {
          if (response.data.carga) {
            this.listaPlantillas.push(data.datos);
            this.cambiaPlantilla(this.listaPlantillas.length - 1);
          } else {
            alert('Error al cargar la plantilla');
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    cargaPlantilla() {
      if (this.listaPlantillas.length > 0) {
        this.plantillaActiva = this.listaPlantillas[this.numPlantilla];
        const rutaPlantilla =
          this.rutaAPI + 'plantillas/' + this.plantillaActiva.archivo;
        axios
          .get(rutaPlantilla, { responseType: 'arraybuffer' })
          .then((resp) => {
            mammoth
              .convertToHtml({ arrayBuffer: resp.data })
              .then((result) => {
                this.contenidoPlantilla = result.value;
                this.construyePlantilla();
              })
              .done();
          });
      }
    },
    cambiaPlantilla(num) {
      this.numPlantilla = num;
      this.cargaPlantilla();
    },
    cambiaTab(numTab) {
      this.tabActiva = numTab;
    },
    construyePlantilla() {
      const rxTextos = /\$\{[^\s]+_[a-z]+\}/g;
      const rxVar = /\$\{([^\s]+)_([a-z]+)\}/g;
      const campos = this.contenidoPlantilla.match(rxTextos);
      this.textos = this.contenidoPlantilla.split(rxTextos);
      this.form = [];
      if (campos && campos.length > 0) {
        for (let i = 0; i < campos.length; i++) {
          const nombreCampo = campos[i].replace(rxVar, '$1');
          const labelCampo = nombreCampo.replaceAll('-', ' ');
          const tipoCampo = campos[i].replace(rxVar, '$2');
          const yaIncluida = this.form.findIndex(
            (item) => item.nombre === nombreCampo && item.tipo === tipoCampo
          );
          if (yaIncluida === -1) {
            this.form.push({
              id: 'id' + i,
              nombre: nombreCampo,
              label: labelCampo,
              tipo: tipoCampo,
              valor: ''
            });
          } else {
            this.form.push({ id: this.form[yaIncluida].id });
          }
        }
      }
    },
    creaWord() {
      const params = new FormData();
      this.form.forEach((campo) => {
        if (campo.nombre)
          params.append(campo.nombre + '_' + campo.tipo, campo.valor);
      });
      params.append('numPlantilla', this.numPlantilla);
      axios
        .post(this.rutaAPI + 'genera.php', params)
        .then((response) => {
          this.nombreArchivo = response.data.nombre;
          this.vinculo = this.rutaAPI + this.nombreArchivo;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    descargado() {
      axios
        .get(this.rutaAPI + 'elimina.php', {
          params: {
            nombreArchivo: this.nombreArchivo
          }
        })
        .then((response) => {
          if (response.data.exito) {
            window.location.reload();
          } else {
            console.log('No se pudo eliminar el archivo');
          }
        })
        .catch((error) => console.log(error));
    },
    limpiar() {
      this.form.forEach((campo) => {
        campo.valor = '';
      });
    }
  }
};
</script>