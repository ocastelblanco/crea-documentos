<template>
  <div class="formulario">
    <h2>Campos de la plantilla</h2>
    <ui-form type="|" item-margin-bottom="16" action-align="center">
      <template #default="{ actionClass }">
        <template v-for="campo in form" :key="campo">
          <div class="campo" v-if="campo.tipo">
            <p class="label">{{ campo.label }}</p>
            <ui-datepicker
              v-if="campo.tipo === 'fecha'"
              fullwidth
              v-model="campo.valor"
              :config="config"
              :placeholder="campo.label"
              toggle
              :id="campo.id"
            ></ui-datepicker>
            <ui-textfield
              v-if="campo.tipo === 'texto'"
              fullwidth
              :id="campo.id"
              v-model="campo.valor"
              :placeholder="campo.label"
            ></ui-textfield>
            <ui-textfield
              v-if="campo.tipo === 'area'"
              v-model="campo.valor"
              input-type="textarea"
              fullwidth
              :placeholder="campo.label"
              maxlength="140"
              rows="8"
            ></ui-textfield>
          </div>
        </template>
        <ui-form-field class="accion" :class="actionClass">
          <ui-button raised @click="crear">Crear documento</ui-button>
          <ui-button outlined @click="limpiar">Limpiar formulario</ui-button>
        </ui-form-field>
      </template>
    </ui-form>
  </div>
</template>
<script>
import lang from 'flatpickr/dist/l10n/es';
export default {
  name: 'formulario',
  props: ['form', 'plantilla'],
  data() {
    return {
      config: {
        dateFormat: 'd \\de F \\de Y',
        defaultDate: 'today',
        locale: lang.es
      }
    };
  },
  emits: ['crear', 'limpiar'],
  methods: {
    crear() {
      this.$emit('crear');
    },
    limpiar() {
      this.$emit('limpiar');
    }
  }
};
</script>