<template>
  <div>
    <h2 style="font-size: 1.4rem; color: var(--text-main); margin-bottom: 1.5rem;">Event Checkout Wireframe</h2>

    <ThreeSplitTemplate>
      <template #left>
        <BasePanel title="Event Info">
          <p style="color: var(--text-main); font-weight: 600;">EventLab Tech Conference 2026</p>
          <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem;">Date: August 15, 2026</p>
          <p style="color: var(--text-muted); font-size: 0.85rem;">Location: Main Auditorium</p>
          <div style="margin-top: 1rem;">
            <UserAvatar initials="EL" name="Event Producer" />
          </div>
        </BasePanel>
      </template>

      <template #center>
        <BasePanel title="Checkout Details">
          <TextInput v-model="customerName" label="Full Name" placeholder="Jane Doe" />
          <TextInput v-model="customerEmail" label="Email Address" placeholder="jane@example.com" />
          <NumberInput v-model="ticketCount" label="Number of Tickets" :min="1" :max="10" />
          
          <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
            <ClickButton variant="success" @click="completeCheckout">Complete Order</ClickButton>
          </div>
        </BasePanel>
      </template>

      <template #right>
        <BasePanel title="Order Summary">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted);">
            <span>Tickets (x{{ ticketCount }}):</span>
            <span>${{ ticketCount * 50 }}</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted);">
            <span>Service Fee:</span>
            <span>$5</span>
          </div>
          <hr style="border: none; border-top: 1px solid var(--border-color); margin: 0.75rem 0;" />
          <div style="display: flex; justify-content: space-between; font-weight: 700; color: var(--text-main);">
            <span>Total:</span>
            <span>${{ (ticketCount * 50) + 5 }}</span>
          </div>
        </BasePanel>
      </template>
    </ThreeSplitTemplate>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from '@/composables/useToast'

const { addToast } = useToast()

const customerName = ref('')
const customerEmail = ref('')
const ticketCount = ref(2)

function completeCheckout() {
  if (!customerName.value) {
    addToast('Please enter your name', 'warning')
    return
  }
  addToast(`Order placed successfully for ${customerName.value}!`, 'success')
}
</script>
