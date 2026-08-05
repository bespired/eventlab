<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'complete'])

const digits = ref(['', '', '', '', '', ''])
const inputRefs = ref([])

// When external modelValue changes, update our digits
watch(() => props.modelValue, (newVal) => {
    const valStr = newVal ? String(newVal) : ''
    const chars = valStr.split('').slice(0, 6)
    for (let i = 0; i < 6; i++) {
        digits.value[i] = chars[i] || ''
    }
}, { immediate: true })

const updateModelValue = () => {
    const val = digits.value.join('')
    emit('update:modelValue', val)
    if (val.length === 6) {
        emit('complete', val)
    }
}

const focusInput = (index) => {
    if (index < 0 || index > 5) return
    const el = inputRefs.value[index]
    if (!el) return
    if (typeof el.focus === 'function') {
        el.focus()
        if (typeof el.select === 'function') {
            el.select()
        }
    } else if (el.$el) {
        const inputEl = el.$el.querySelector('input')
        if (inputEl) {
            inputEl.focus()
            inputEl.select()
        }
    }
}

const onInput = (index, val) => {
    const valStr = val != null ? String(val) : ''

    if (!valStr) {
        digits.value[index] = ''
        updateModelValue()
        return
    }

    const char = valStr.slice(-1)
    digits.value[index] = char
    updateModelValue()

    if (char && index < 5) {
        nextTick(() => {
            focusInput(index + 1)
        })
    }
}

const onKeydown = (index, event) => {
    if (event.key === 'Backspace') {
        if (!digits.value[index] && index > 0) {
            event.preventDefault()
            digits.value[index - 1] = ''
            updateModelValue()
            nextTick(() => {
                focusInput(index - 1)
            })
        }
    } else if (event.key === 'ArrowLeft' && index > 0) {
        event.preventDefault()
        focusInput(index - 1)
    } else if (event.key === 'ArrowRight' && index < 5) {
        event.preventDefault()
        focusInput(index + 1)
    }
}

const onPaste = (index, event) => {
    event.preventDefault()
    const clipboardData = event.clipboardData || window.clipboardData
    if (!clipboardData) return
    const pastedData = clipboardData.getData('text').trim().slice(0, 6)
    if (!pastedData) return

    const chars = pastedData.split('')
    for (let i = 0; i < 6; i++) {
        digits.value[i] = chars[i] || ''
    }
    updateModelValue()

    const nextIndex = Math.min(chars.length, 5)
    nextTick(() => {
        focusInput(nextIndex)
    })
}
</script>

<template>
    <div class="six-digits-input">
        <text-input
            v-for="(digit, index) in digits"
            :key="`digit-${index}`"
            :ref="el => inputRefs[index] = el"
            :model-value="digit"
            type="text"
            class="digit-input"
            maxlength="2"
            inputmode="numeric"
            @update:model-value="val => onInput(index, val)"
            @keydown="onKeydown(index, $event)"
            @paste="onPaste(index, $event)"
        />
    </div>
</template>

<style>
.six-digits-input {
    display: flex;
    justify-content: space-between;
    gap: 6px;
}

:deep(.digit-input) {
    width: 44px;
    height: 44px;
    font-size: 1.25rem;
    font-weight: 600;
    text-align: center;
    padding: 0;
}
</style>
