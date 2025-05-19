<template>
  <div class="container-fluid py-5 bg-light">
    <div class="row justify-content-center">
      <div class="col-lg-11">
        <h2 class="mb-4 text-primary fw-bold">
          <i class="pi pi-clock me-2"></i>TimeFlow
        </h2>
        
        <div class="row g-4">
          <!-- Left Column: Timer and Form -->
          <div class="col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
              <div class="card-header bg-gradient-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="pi pi-stopwatch me-2"></i>Time Tracker</h5>
                <span v-if="isRunning" class="badge bg-light text-primary animate-pulse">Recording</span>
              </div>
              <div class="card-body p-4">
                <!-- Timer Display -->
                <div class="timer-display text-center mb-4 p-3 bg-light rounded-4">
                  <h2 class="display-4 mb-0 fw-bold text-primary">{{ formatTimeDisplay(displayTime) }}</h2>
                  <p class="text-muted mb-0">{{ isRunning ? 'Timer running...' : 'Timer stopped' }}</p>
                </div>
                
                <div class="d-grid gap-2 mb-4">
                  <button @click="toggleTimer" class="btn btn-lg" :class="isRunning ? 'btn-danger' : 'btn-primary'">
                    <i :class="isRunning ? 'pi pi-pause me-2' : 'pi pi-play me-2'"></i>
                    {{ isRunning ? 'Stop' : 'Start' }} Timer
                  </button>
                  <p v-if="hasSavedTime" class="text-center text-muted small mt-1">
                    Last saved time: {{ formatTimeDisplay(savedTime) }}
                  </p>
                </div>

                <div class="project-selection mb-4">
                  <label class="form-label fw-bold text-primary">
                    <i class="pi pi-folder me-2"></i>Project & Task
                  </label>
                  <div class="input-group mb-3">
                    <span class="input-group-text bg-light"><i class="pi pi-search"></i></span>
                    <input 
                      type="text" 
                      class="form-control form-control-lg border-start-0" 
                      placeholder="Search projects or tasks..." 
                      v-model="searchQuery"
                    />
                  </div>
                  
                  <div class="border rounded-3 shadow-sm bg-white">
                    <Tree 
                      :value="projectTreeNodes" 
                      :filter="true"
                      :filterMode="'strict'"
                      :filterBy="'label'"
                      selectionMode="single"
                      v-model:selectionKeys="selectedNodeKey"
                      class="p-2 custom-tree"
                      @node-select="onNodeSelect"
                      :pt="{ container: { class: 'max-h-72 overflow-y-auto' } }"
                    >
                      <template #default="{ node }">
                        <div class="d-flex align-items-center p-1">
                          <span v-if="node.type === 'project'" class="project-icon me-2">
                            <i class="pi pi-folder-open text-primary"></i>
                          </span>
                          <span v-else class="task-icon me-2">
                            <i class="pi pi-file text-secondary"></i>
                          </span>
                          <span class="fw-medium">{{ node.label }}</span>
                          <span v-if="node.type === 'project'" class="ms-2 badge bg-light text-secondary">{{ node.key }}</span>
                        </div>
                      </template>
                    </Tree>
                  </div>
                  
                  <div v-if="selectedProject || selectedTask" class="mt-3 p-3 rounded-3 bg-light border-start border-4 border-primary">
                    <div v-if="selectedProject" class="mb-1">
                      <span class="fw-bold text-primary"><i class="pi pi-folder me-1"></i> Project:</span> 
                      <span>{{ selectedProject.name }}</span>
                      <span class="badge bg-primary ms-2">{{ selectedProject.key }}</span>
                    </div>
                    <div v-if="selectedTask" class="text-truncate">
                      <span class="fw-bold text-secondary"><i class="pi pi-file me-1"></i> Task:</span> 
                      <span>{{ selectedTask.summary }}</span>
                    </div>
                  </div>
                </div>

                <div class="mb-4">
                  <label for="description" class="form-label fw-bold text-primary">
                    <i class="pi pi-pencil me-2"></i>Description
                  </label>
                  <textarea
                    id="description" 
                    class="form-control form-control-lg" 
                    v-model="description" 
                    placeholder="What are you working on?" 
                    rows="2"
                  ></textarea>
                </div>

                <button @click="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow-sm">
                  <i class="pi pi-check-circle me-2"></i>Save Time Entry
                </button>
              </div>
            </div>
          </div>

          <!-- Right Column: Time Table and Charts -->
          <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
              <div class="card-header bg-gradient-secondary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="pi pi-list me-2"></i>Recent Entries</h5>
                <button class="btn btn-sm btn-light">
                  <i class="pi pi-calendar me-1"></i>View All
                </button>
              </div>
              <div class="card-body p-0">
                <DataTable 
                  :value="trackings" 
                  v-if="trackings.length" 
                  class="table-striped"
                  responsiveLayout="scroll"
                  :rowHover="true"
                  stripedRows
                  paginator
                  :rows="5"
                  :rowsPerPageOptions="[5, 10, 20]"
                >
                  <Column field="created_at" header="Date">
                    <template #body="slotProps">
                      <div class="d-flex flex-column">
                        <span class="fw-medium">{{ formatDateDay(slotProps.data.created_at) }}</span>
                        <small class="text-muted">{{ formatDateTime(slotProps.data.created_at) }}</small>
                      </div>
                    </template>
                  </Column>
                  <Column field="project_key" header="Project">
                    <template #body="slotProps">
                      <span class="badge bg-primary">{{ slotProps.data.project_key }}</span>
                    </template>
                  </Column>
                  <Column field="task" header="Task">
                    <template #body="slotProps">
                      <span class="text-truncate d-inline-block" style="max-width: 150px">
                        {{ slotProps.data.task || '-' }}
                      </span>
                    </template>
                  </Column>
                  <Column field="time" header="Time">
                    <template #body="slotProps">
                      <span class="fw-bold">{{ formatTimeDisplay(slotProps.data.time) }}</span>
                    </template>
                  </Column>
                  <Column field="description" header="Description">
                    <template #body="slotProps">
                      <span class="text-truncate d-inline-block" style="max-width: 200px">
                        {{ slotProps.data.description || 'No description' }}
                      </span>
                    </template>
                  </Column>
                  <Column header="Actions" :exportable="false" style="min-width:8rem">
                    <template #body="slotProps">
                      <div class="d-flex gap-2">
                        <button @click="resumeTracking(slotProps.data)" class="btn btn-sm btn-outline-primary" title="Resume this time entry">
                          <i class="pi pi-play me-1"></i>Resume
                        </button>
                        <button @click="deleteTracking(slotProps.data.id)" class="btn btn-sm btn-outline-danger" title="Delete this time entry">
                          <i class="pi pi-trash"></i>
                        </button>
                      </div>
                    </template>
                  </Column>
                </DataTable>
                <div v-else class="p-5 text-center">
                  <div class="empty-state-icon mb-3">
                    <i class="pi pi-inbox text-secondary" style="font-size: 3.5rem; opacity: 0.5"></i>
                  </div>
                  <p class="text-muted mb-0">No time entries recorded yet.</p>
                </div>
              </div>
            </div>

            <!-- Analytics Cards -->
            <div class="row g-4">
              <div class="col-md-6">
              <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-header bg-gradient-info text-white py-3 px-4">
                <h5 class="mb-0"><i class="pi pi-chart-bar me-2"></i>Weekly Summary</h5>
                </div>
                <div class="card-body p-4">
                <!-- Chart component replaces the placeholder -->
                <Chart type="bar" :data="weeklyChartData" :options="weeklyChartOptions" class="w-100 h-100" />
                <p class="text-center text-muted mt-3 small">Hours tracked per day this week</p>
                </div>
              </div>
              </div>
              <div class="col-md-6">
              <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-header bg-gradient-success text-white py-3 px-4">
                <h5 class="mb-0"><i class="pi pi-check-circle me-2"></i>Productivity</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                <div class="chart-placeholder bg-light rounded-3 d-flex justify-content-center align-items-center w-100" style="height: 180px">
                  <i class="pi pi-chart-line text-success" style="font-size: 3rem"></i>
                </div>
                <p class="text-center text-muted mt-3">Productivity trends will appear here</p>
                </div>
              </div>
              </div>
            </div>
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

// Format time to display as HH:MM:SS
const formatTimeDisplay = (seconds) => {
  if (!seconds && seconds !== 0) return '00:00:00'
  
  const hrs = Math.floor(seconds / 3600)
  const mins = Math.floor((seconds % 3600) / 60)
  const secs = seconds % 60
  
  return [
    hrs.toString().padStart(2, '0'),
    mins.toString().padStart(2, '0'),
    secs.toString().padStart(2, '0')
  ].join(':')
}

// Format date display
const formatDateDay = (str) => {
  const date = new Date(str)
  return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
}

const formatDateTime = (str) => {
  const date = new Date(str)
  return date.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' })
}

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

const resumeTracking = (trackingEntry) => {
  // Set the selected project and task from the tracking entry
  if (trackingEntry.project_key) {
    selectedProject.value = props.projects.find(p => p.key === trackingEntry.project_key) || null
  }
  
  // Set the task if available
  if (trackingEntry.task) {
    // Find the corresponding task in the project
    if (selectedProject.value && selectedProject.value.issues) {
      selectedTask.value = selectedProject.value.issues.find(issue => 
        issue.summary === trackingEntry.task || issue === trackingEntry.task
      ) || { summary: trackingEntry.task }
    } else {
      selectedTask.value = { summary: trackingEntry.task }
    }
  }
  
  // Set the description
  description.value = trackingEntry.description || ''
  
  // Reset time and start the timer
  elapsedTime.value = 0
  savedTime.value = null
  
  // Start the timer automatically
  if (!isRunning.value) {
    toggleTimer()
  }
  
  // Scroll to timer section on mobile
  if (window.innerWidth < 992) {
    document.querySelector('.timer-display')?.scrollIntoView({ behavior: 'smooth' })
  }
}

const deleteTracking = async (trackingId) => {
  if (!confirm('Are you sure you want to delete this time entry?')) return
  
  try {
    await axios.delete(`/tracking/${trackingId}`)
    // Remove the deleted entry from the list
    trackings.value = trackings.value.filter(t => t.id !== trackingId)
  } catch (err) {
    console.error('Failed to delete time entry:', err)
    alert('Failed to delete time entry. Please try again.')
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
</script>

<style>
/* Additional styles for enhanced design */
.bg-gradient-primary {
  background: linear-gradient(135deg, #4361ee, #3f37c9);
}

.bg-gradient-secondary {
  background: linear-gradient(135deg, #4a4e69, #22223b);
}

.bg-gradient-info {
  background: linear-gradient(135deg, #4cc9f0, #4361ee);
}

.bg-gradient-success {
  background: linear-gradient(135deg, #2ec4b6, #0096c7);
}

.rounded-4 {
  border-radius: 1rem !important;
}

.animate-pulse {
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.custom-tree .p-tree-container .p-treenode {
  padding: 0.25rem 0;
}

.custom-tree .p-treenode-content:hover {
  background-color: #f8f9fa;
  border-radius: 0.375rem;
}

.custom-tree .p-treenode-content.p-highlight {
  background-color: #e7f1ff;
  border-radius: 0.375rem;
}

.max-h-72 {
  max-height: 18rem;
}

.timer-display {
  transition: all 0.3s ease;
}

.chart-placeholder {
  transition: all 0.3s ease;
}

.chart-placeholder:hover {
  transform: scale(1.02);
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.02);
}

.card {
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(-3px);
}
</style>