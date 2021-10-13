<template>
  <div class="contenedor">
    <ui-grid class="grid">
      <ui-grid-cell class="celda seleccionar" columns="4">
        <div class="seleccion" v-if="lista.length > 0">
          <p class="label">Usar plantilla</p>
          <ui-select
            fullwidth
            v-model="numPlant"
            :options="lista"
            @selected="cambiaPlantilla($event)"
          >
          </ui-select>
          <div class="descripcion">
            <h3>Plantilla actual</h3>
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
          <div class="accion">
            <ui-button raised icon="delete" @click="borraPlantilla">
              Eliminar plantilla seleccionada
            </ui-button>
          </div>
        </div>
        <ui-alert v-else state="warning">
          Agregue una nueva plantilla en el área correspondiente
        </ui-alert>
      </ui-grid-cell>
      <ui-grid-cell class="celda drop" columns="8">
        <div
          class="area-drop"
          @dragover="dragover"
          @dragleave="dragleave"
          @drop="drop"
          @click="uploadFile"
          v-if="nuevaPlantilla === null"
        >
          <input
            id="upload"
            type="file"
            @change="cargaArchivo"
            accept=".docx"
          />
          <ui-icon size="48">post_add</ui-icon>
          <p>Arrastrar y soltar para añadir una plantilla nueva</p>
        </div>
        <div class="nueva-plantilla" v-if="nuevaPlantilla">
          <h3>Información de la nueva plantilla</h3>
          <label class="label">Nombre</label>
          <ui-textfield
            v-model="datosNuevaPlantilla.nombre"
            fullwidth
            placeholder="Nombre de la plantilla"
            maxlength="40"
          ></ui-textfield>
          <label class="label">Descripción</label>
          <ui-textfield
            v-model="datosNuevaPlantilla.descripcion"
            input-type="textarea"
            fullwidth
            placeholder="Descripción de la plantilla"
            maxlength="140"
            rows="8"
          ></ui-textfield>
          <div class="accion">
            <ui-button
              class="boton-subir"
              v-if="
                datosNuevaPlantilla.descripcion && datosNuevaPlantilla.nombre
              "
              raised
              @click="cargaNuevaPlantilla"
              icon="upload"
            >
              Subir nueva plantilla al servidor
            </ui-button>
          </div>
        </div>
      </ui-grid-cell>
    </ui-grid>
  </div>
</template>
<script>
export default {
  name: 'gestiona',
  data() {
    return {
      lista: [],
      numPlant: 0,
      nuevaPlantilla: null,
      datosNuevaPlantilla: {}
    };
  },
  updated() {
    this.cargaData(this.listaPlantillas, this.numPlantilla);
  },
  props: ['listaPlantillas', 'numPlantilla', 'plantilla'],
  emits: ['cambiaPlantilla', 'cargaNuevaPlantilla', 'eliminarPlantilla'],
  methods: {
    borraPlantilla() {
      this.$confirm({
        message: '¿Eliminar la plantilla seleccionada?',
        acceptText: 'Eliminar',
        cancelText: 'Cancelar'
      }).then((res) => {
        if (res) {
          this.$emit('eliminarPlantilla', this.numPlant);
        }
      });
    },
    cargaNuevaPlantilla() {
      this.datosNuevaPlantilla.archivo = this.nuevaPlantilla.name;
      this.$emit('cargaNuevaPlantilla', {
        archivo: this.nuevaPlantilla,
        datos: this.datosNuevaPlantilla
      });
      this.nuevaPlantilla = null;
      this.datosNuevaPlantilla = {};
    },
    cambiaPlantilla(ev) {
      this.$emit('cambiaPlantilla', ev.value);
    },
    drop(ev) {
      ev.preventDefault();
      ev.currentTarget.classList.remove('dragover');
      if (
        ev.dataTransfer.files[0].type ===
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
      ) {
        this.nuevaPlantilla = ev.dataTransfer.files[0];
      }
    },
    dragover(ev) {
      ev.preventDefault();
      ev.currentTarget.classList.add('dragover');
    },
    dragleave(ev) {
      ev.currentTarget.classList.remove('dragover');
    },
    cargaArchivo(ev) {
      this.nuevaPlantilla = ev.target.files[0];
    },
    uploadFile() {
      document.getElementById('upload').click();
    },
    cargaData(_lista, _num) {
      this.numPlant = _num;
      this.lista = [];
      _lista.forEach((p, i) => this.lista.push({ label: p.nombre, value: i }));
    }
  },
  watch: {
    numPlantilla(val) {
      this.cargaData(this.listaPlantillas, val);
    }
  }
};
</script>