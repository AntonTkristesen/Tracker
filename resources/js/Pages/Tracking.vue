<template>
  <div class="container-fluid py-4">
    <div class="row">
      <!-- Left Column: Timer and Form -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            Time Tracking
          </div>
          <div class="card-body">
            <h5 class="card-title">Timer: {{ displayTime }}s</h5>
            <button @click="toggleTimer" class="btn btn-outline-primary mb-3">
              {{ isRunning ? 'Stop' : 'Start' }} Timer
            </button>
            <p v-if="hasSavedTime" class="text-muted">Last saved time: {{ savedTime }}s</p>

            <!-- Project/Task Selector -->
            <div class="mb-3">
              <label class="form-label fw-bold">Select Project/Task</label>
              <input 
                type="text" 
                class="form-control mb-2" 
                placeholder="Search by project, task or client" 
                v-model="searchQuery"
              />
              <Tree 
                :value="projectTreeNodes" 
                :filter="true"
                :filterMode="'strict'"
                :filterPlaceholder="'Search'"
                :filterBy="'label'"
                selectionMode="single"
                v-model:selectionKeys="selectedNodeKey"
                class="border rounded p-2"
                @node-select="onNodeSelect"
                :pt="{ container: { class: 'max-h-72 overflow-y-auto' } }"
              >
                <template #default="{ node }">
                  <div class="d-flex align-items-center">
                    <span v-if="node.type === 'project'" class="text-primary me-2">●</span>
                    <span v-else class="me-2">📄</span>
                    <span>{{ node.label }}</span>
                    <small v-if="node.type === 'project'" class="ms-2 text-muted">({{ node.key }})</small>
                  </div>
                </template>
              </Tree>
              <div v-if="selectedProject || selectedTask" class="mt-2 border rounded p-2 bg-light">
                <div v-if="selectedProject"><strong>Project:</strong> {{ selectedProject.name }} ({{ selectedProject.key }})</div>
                <div v-if="selectedTask"><strong>Task:</strong> {{ selectedTask.summary }}</div>
              </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label for="description" class="form-label fw-bold">Description</label>
              <input 
                id="description" 
                type="text" 
                class="form-control" 
                v-model="description" 
                placeholder="What are you tracking?" 
              />
            </div>

            <button @click="submit" class="btn btn-success w-100">
              Register Time Track
            </button>
          </div>
        </div>
      </div>

      <!-- Right Column: Time Table and Charts -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-secondary text-white">
            Previous Time Trackings
          </div>
          <div class="card-body">
            <DataTable 
              :value="trackings" 
              v-if="trackings.length" 
              class="table table-striped table-hover"
              responsiveLayout="scroll"
            >
              <Column field="created_at" header="Date">
                <template #body="slotProps">
                  {{ formatDate(slotProps.data.created_at) }}
                </template>
              </Column>
              <Column field="project_key" header="Project" />
              <Column field="task" header="Task" />
              <Column field="time" header="Time">
                <template #body="slotProps">
                  {{ slotProps.data.time }}s
                </template>
              </Column>
              <Column field="description" header="Description">
                <template #body="slotProps">
                  {{ slotProps.data.description || 'No description' }}
                </template>
              </Column>
            </DataTable>
            <p v-else class="text-muted">No time trackings yet.</p>
          </div>
        </div>

        <!-- Placeholder for future charts -->
        <div class="card shadow-sm">
          <div class="card-header bg-info text-white">
            Analytics (Coming Soon)
          </div>
          <div class="card-body text-center text-muted">
            Charts will be shown here...
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const trackings = ref(props.trackings || [])

// Time tracking
const elapsedTime = ref(0)
const savedTime = ref(null)
const description = ref('')
const isRunning = ref(false)
let interval = null

// Project and task selection
const searchQuery = ref('')
const selectedNodeKey = ref(null)
const selectedProject = ref(null)
const selectedTask = ref(null)

const displayTime = computed(() => savedTime.value ?? elapsedTime.value)
const hasSavedTime = computed(() => savedTime.value !== null)

// Convert projects to tree nodes format for PrimeVue Tree
const projectTreeNodes = computed(() => {
  if (!props.projects) return []
  
  return props.projects.map(project => {
    // Create a node for each project
    const projectNode = {
      key: `project-${project.key}`,
      label: project.name,
      type: 'project',
      id: project.id,
      key: project.key,
      children: [],
      selectable: true,
      leaf: project.issues.length === 0
    }

    if (project.issues && project.issues.length) {
      projectNode.children = project.issues.map(issue => ({
        key: `issue-${issue.key || issue.id}`,
        label: issue.summary || issue,
        type: 'issue',
        project: project.key,
        data: issue,
        selectable: true,
        leaf: true
      }))
    }
    
    return projectNode
  })
})

// Filter tree based on search query
watch(searchQuery, (newQuery) => {
  if (!newQuery) return

  // This would be handled by PrimeVue's built-in filtering
  // But you can add custom filtering logic here if needed
})

const toggleTimer = () => {
  if (isRunning.value) {
    clearInterval(interval)
    savedTime.value = elapsedTime.value
  } else {
    interval = setInterval(() => elapsedTime.value++, 1000)
  }
  isRunning.value = !isRunning.value
}

const onNodeSelect = (node) => {
  if (node.type === 'project') {
    // Selected a project
    selectedProject.value = props.projects.find(p => p.key === node.key)
    selectedTask.value = null
  } else if (node.type === 'issue') {
    // Selected a task/issue
    selectedProject.value = props.projects.find(p => p.key === node.project)
    selectedTask.value = node.data
  }
}

const submit = async () => {
  if (isRunning.value) toggleTimer()

  const timeToSave = displayTime.value
  if (timeToSave <= 0) return

  try {
    const { data } = await axios.post('/tracking', {
      time: timeToSave,
      project_key: selectedProject.value ? selectedProject.value.key : null,
      task: selectedTask.value ? selectedTask.value.summary || selectedTask.value : null,
      description: description.value
    })

    if (data.tracking) {
      trackings.value.unshift(data.tracking)
    }

    // Reset
    elapsedTime.value = 0
    savedTime.value = null
    description.value = ''
    // Don't reset selection to allow continuous tracking for the same task
  } catch (err) {
    console.error('Failed to submit time:', err)
  }
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/user-trackings')
    trackings.value = data.trackings
  } catch (err) {
    console.error('Failed to load trackings:', err)
  }
})

onUnmounted(() => clearInterval(interval))

const formatDate = (str) => new Date(str).toLocaleString()
</script>

<style>
/* You may need to import PrimeVue CSS */
/* @import 'primevue/resources/themes/lara-light-blue/theme.css'; */
/* @import 'primevue/resources/primevue.min.css'; */
/* @import 'primeicons/primeicons.css'; */

.max-h-72 {
  max-height: 18rem;
}

.w-100 {
  width: 100%;
}
</style>