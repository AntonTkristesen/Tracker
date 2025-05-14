<template>
    <div>
      <p>Time: {{ elapsedTime }}s</p>
      <Button @click="toggleTimer">{{ running ? 'Stop' : 'Start' }} Timer</Button>
      <p v-if="savedTime !== null">Last saved time: {{ savedTime }}s</p>
       <Button @click="submit" class="btn btn-primary">Register Time track</Button> 
    </div>
  </template>
  
  <script setup>
  import { ref, onUnmounted } from 'vue'
  import axios from 'axios'
  
  const elapsedTime = ref(0)
  const running = ref(false)
  const savedTime = ref(null)
  let intervalId = null
  
  const toggleTimer = () => {
    if (running.value) {
      clearInterval(intervalId)
      savedTime.value = elapsedTime.value
      running.value = false
    } else {
      intervalId = setInterval(() => {
        elapsedTime.value += 1
      }, 1000)
      running.value = true
    }
  }

  const submit = async () => {
    console.log('Submitting time:', savedTime.value ?? elapsedTime.value)
  try {
    const response = await axios.post('/tracking', {
      time: savedTime.value ?? elapsedTime.value
    })
    console.log('Time submitted successfully:', response.data)
  } catch (error) {
    console.error('Failed to submit time:', error)
  }}
  onUnmounted(() => {
    clearInterval(intervalId)
  })
  </script>
  