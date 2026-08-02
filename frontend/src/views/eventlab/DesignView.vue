<template>
    <div class="white-padding">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <NotificationBanner variant="success" message="Vue 3 SPA component library successfully initialized with Vite and unplugin-vue-components autoloading!" />
            <BasePanel title="1. Button Primitives & Interactive Hover/Active Filters">
                <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.9rem;">
                    Hover over buttons for <strong>+15% brightness</strong> and click for <strong>-15% brightness</strong> filters.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
                    <ClickButton variant="primary" @click="notify('Primary Clicked!')">Primary Button</ClickButton>
                    <ClickButton variant="secondary" @click="notify('Secondary Clicked!')">Secondary Button</ClickButton>
                    <ClickButton variant="success" @click="notify('Success Action!')">Success Button</ClickButton>
                    <ClickButton variant="warning" @click="notify('Warning Action!')">Warning Button</ClickButton>
                    <ClickButton variant="cancel" @click="notify('Cancel Action!')">Cancel Button</ClickButton>
                    <ClickButton variant="disabled">Disabled Button</ClickButton>
                    <IconButton title="Settings Icon Button" @click="notify('Settings Clicked')">⚙️</IconButton>
                    <ToggleButton v-model="toggleState" label="Interactive Toggle" />
                </div>
            </BasePanel>
            <SlotTwoSplitTemplate>
                <template #left>
                    <BasePanel title="2. Input Components">
                        <TextInput v-model="textValue" label="Text Input (.text-input)" placeholder="Enter name..." />
                        <NumberInput v-model="numberValue" label="Number Input (.number-input)" :min="1" :max="100" />
                        <DateInput v-model="dateValue" label="Date Input (.date-input)" />
                        <TextArea v-model="areaValue" label="Text Area (.text-area)" placeholder="Enter description..." />
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
            <ToggleInput v-model="checkValue" label="Agree to terms" />
            <RadioButton v-model="radioValue" value="optionA" name="opts" label="Option A" />
            <RadioButton v-model="radioValue" value="optionB" name="opts" label="Option B" />
          </div>
        </BasePanel>
      </template>

      <template #right>
        <BasePanel title="3. Picker Components (Placeholders)">
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <ColorPicker v-model="selectedColor" />
            <DateRangePicker />
            <ImageOptionPicker />
            <TagPicker />
            <SwatchPicker />
          </div>
        </BasePanel>
      </template>
    </SlotTwoSplitTemplate>

    <BasePanel title="4. Panels & Modal Triggers">
      <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <ClickButton variant="primary" @click="showConfirm = true">Open Confirm Dialog</ClickButton>
        <ClickButton variant="success" @click="showNote = true">Open Note Dialog</ClickButton>
        <ClickButton variant="secondary" @click="showFloat = !showFloat">Toggle Float Panel</ClickButton>
      </div>

      <div v-if="showFloat" style="margin-top: 1rem;">
        <FloatPanel title="Dynamic Float Panel (.float-panel)" @close="showFloat = false">
          <p style="color: var(--text-muted); font-size: 0.9rem;">
            This floating panel overlay uses high elevation shadows and border tokens.
          </p>
        </FloatPanel>
      </div>
    </BasePanel>

    <!-- Dialog Modals -->
    <ConfirmModal
      v-model:is-open="showConfirm"
      title="Confirm Action"
      message="Would you like to trigger a toast message?"
      @confirm="onConfirmAction"
    />

    <NoteModal
      v-model:is-open="showNote"
      title="Create New Note"
      @save="onSaveNote"
    />
  </div>
</div>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from '@/composables/useToast'

const { addToast } = useToast()

const toggleState = ref(true)
const textValue = ref('EventLab GUI')
const numberValue = ref(42)
const dateValue = ref('2026-07-29')
const areaValue = ref('Sample text content')
const checkValue = ref(true)
const radioValue = ref('optionA')
const selectedColor = ref('#3b82f6')

const showConfirm = ref(false)
const showNote = ref(false)
const showFloat = ref(false)

function notify(msg) {
  addToast(msg, 'info')
}

function onConfirmAction() {
  addToast('Action Confirmed!', 'success')
}

function onSaveNote(note) {
  addToast(`Saved Note: "${note}"`, 'success')
}
</script>