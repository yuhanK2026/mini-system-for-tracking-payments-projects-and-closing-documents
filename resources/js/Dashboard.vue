<template>
  <div class="dashboard-container">
    <h1>Payment Tracking Dashboard</h1>
    
    <!-- Filters Section -->
    <div class="filters-section">
      <h2>Filters</h2>
      <div class="filter-grid">
        <div class="filter-group">
          <label>Project:</label>
          <select v-model="filters.project_id" @change="loadData">
            <option value="">All Projects</option>
            <option v-for="project in filterOptions.projects" :value="project.id" :key="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Client:</label>
          <select v-model="filters.client_id" @change="loadData">
            <option value="">All Clients</option>
            <option v-for="client in filterOptions.clients" :value="client.id" :key="client.id">
              {{ client.name }}
            </option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Act Status:</label>
          <select v-model="filters.act_status" @change="loadData">
            <option value="">All Statuses</option>
            <option v-for="status in filterOptions.act_statuses" :value="status.value" :key="status.value">
              {{ status.label }}
            </option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Start Date:</label>
          <input type="date" v-model="filters.start_date" @change="loadData" />
        </div>
        
        <div class="filter-group">
          <label>End Date:</label>
          <input type="date" v-model="filters.end_date" @change="loadData" />
        </div>
        
        <div class="filter-group">
          <label>Search:</label>
          <input type="text" v-model="filters.search" placeholder="Search by purpose or client..." @input="debounceLoadData" />
        </div>
        
        <div class="filter-group">
          <button @click="resetFilters">Reset Filters</button>
        </div>
      </div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
      <h2>Summary</h2>
      <div class="summary-grid">
        <div class="summary-card">
          <h3>Total Amount</h3>
          <p class="amount">{{ formatCurrency(summary.total_amount) }}</p>
        </div>
        
        <div class="summary-card">
          <h3>Projects</h3>
          <p class="count">{{ summary.projects_count }}</p>
        </div>
        
        <div class="summary-card">
          <h3>Payments</h3>
          <p class="count">{{ summary.payments_count }}</p>
        </div>
        
        <div class="summary-card">
          <h3>Closed Acts Amount</h3>
          <p class="amount">{{ formatCurrency(summary.closed_acts_amount) }}</p>
        </div>
        
        <div class="summary-card">
          <h3>Unclosed Acts Amount</h3>
          <p class="amount">{{ formatCurrency(summary.unclosed_acts_amount) }}</p>
        </div>
        
        <div class="summary-card">
          <h3>No Act Sent</h3>
          <p class="count warning">{{ summary.no_act_sent }}</p>
        </div>
        
        <div class="summary-card">
          <h3>Sent Not Signed</h3>
          <p class="count warning">{{ summary.sent_not_signed }}</p>
        </div>
        
        <div class="summary-card">
          <h3>Attention Required</h3>
          <p class="count danger">{{ summary.attention_required }}</p>
        </div>
      </div>
    </div>
    
    <!-- Projects Table -->
    <div class="projects-section">
      <h2>Projects</h2>
      <table class="data-table">
        <thead>
          <tr>
            <th>Project Name</th>
            <th>Client</th>
            <th>Total Payments</th>
            <th>Payments Count</th>
            <th>Closed Acts</th>
            <th>Unclosed Acts</th>
            <th>Project Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects" :key="project.id">
            <td>{{ project.name }}</td>
            <td>{{ project.client?.name }}</td>
            <td>{{ formatCurrency(project.total_payments || 0) }}</td>
            <td>{{ project.payments_count || 0 }}</td>
            <td>{{ project.closed_acts_count || 0 }}</td>
            <td>{{ project.unclosed_acts_count || 0 }}</td>
            <td :class="getStatusClass(project.status)">{{ project.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Payments Table -->
    <div class="payments-section">
      <h2>Payments</h2>
      <table class="data-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Project</th>
            <th>Client</th>
            <th>Purpose</th>
            <th>Service Stage</th>
            <th>Act Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in payments" :key="payment.id" :class="getPaymentRowClass(payment)">
            <td>{{ formatDate(payment.payment_date) }}</td>
            <td>{{ formatCurrency(payment.amount) }}</td>
            <td>{{ payment.project?.name }}</td>
            <td>{{ payment.project?.client?.name }}</td>
            <td>{{ payment.payment_purpose }}</td>
            <td>{{ payment.service_stage }}</td>
            <td>
              <span :class="getStatusClass(payment.act?.status || 'not_sent')">
                {{ getStatusLabel(payment.act?.status || 'not_sent') }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button v-if="!payment.act?.is_sent" @click="updateActStatus(payment.id, 'send')" class="btn-send">
                  Mark as Sent
                </button>
                <button v-if="payment.act?.is_sent && !payment.act?.is_signed" @click="updateActStatus(payment.id, 'sign')" class="btn-sign">
                  Mark as Signed
                </button>
                <button v-if="payment.act" @click="showCommentModal(payment)" class="btn-comment">
                  Add Comment
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Comment Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <h3>Add Manager Comment</h3>
        <p>Payment: {{ selectedPayment?.payment_purpose }}</p>
        <p>Amount: {{ formatCurrency(selectedPayment?.amount) }}</p>
        <textarea v-model="commentText" placeholder="Enter comment..." rows="4"></textarea>
        <div class="modal-actions">
          <button @click="saveComment">Save</button>
          <button @click="closeModal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      summary: {},
      projects: [],
      payments: [],
      filterOptions: {
        projects: [],
        clients: [],
        act_statuses: []
      },
      filters: {
        project_id: '',
        client_id: '',
        act_status: '',
        start_date: '',
        end_date: '',
        search: ''
      },
      showModal: false,
      selectedPayment: null,
      commentText: '',
      loadTimeout: null
    };
  },
  
  mounted() {
    this.loadData();
  },
  
  methods: {
    async loadData() {
      const params = new URLSearchParams();
      Object.keys(this.filters).forEach(key => {
        if (this.filters[key]) {
          params.append(key, this.filters[key]);
        }
      });
      
      try {
        const res = await axios.get(`/dashboard?${params.toString()}`);
        this.summary = res.data.summary;
        this.projects = res.data.projects;
        this.payments = res.data.payments;
        this.filterOptions = res.data.filters;
      } catch (error) {
        console.error('Error loading data:', error);
        alert('Error loading dashboard data');
      }
    },
    
    debounceLoadData() {
      clearTimeout(this.loadTimeout);
      this.loadTimeout = setTimeout(() => {
        this.loadData();
      }, 500);
    },
    
    resetFilters() {
      this.filters = {
        project_id: '',
        client_id: '',
        act_status: '',
        start_date: '',
        end_date: '',
        search: ''
      };
      this.loadData();
    },
    
    async updateActStatus(paymentId, action) {
      try {
        const payment = this.payments.find(p => p.id === paymentId);
        if (!payment) return;
        
        const actId = payment.act?.id;
        if (!actId) {
          // Create act if it doesn't exist
          const response = await axios.post('/api/acts', {
            payment_id: paymentId,
            is_sent: action === 'send',
            is_signed: action === 'sign'
          });
        } else {
          // Update existing act
          const updates = {};
          if (action === 'send') updates.is_sent = true;
          if (action === 'sign') updates.is_signed = true;
          
          await axios.patch(`/acts/${actId}`, updates);
        }
        
        // Reload data
        this.loadData();
        alert(`Act ${action === 'send' ? 'sent' : 'signed'} successfully`);
      } catch (error) {
        console.error('Error updating act status:', error);
        alert('Error updating act status');
      }
    },
    
    showCommentModal(payment) {
      this.selectedPayment = payment;
      this.commentText = payment.act?.manager_comment || '';
      this.showModal = true;
    },
    
    async saveComment() {
      try {
        if (!this.selectedPayment?.act?.id) {
          // Create act with comment
          await axios.post('/api/acts', {
            payment_id: this.selectedPayment.id,
            manager_comment: this.commentText
          });
        } else {
          // Update comment
          await axios.patch(`/acts/${this.selectedPayment.act.id}`, {
            manager_comment: this.commentText
          });
        }
        
        this.closeModal();
        this.loadData();
        alert('Comment saved successfully');
      } catch (error) {
        console.error('Error saving comment:', error);
        alert('Error saving comment');
      }
    },
    
    closeModal() {
      this.showModal = false;
      this.selectedPayment = null;
      this.commentText = '';
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 2
      }).format(amount);
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('ru-RU');
    },
    
    getStatusLabel(status) {
      const labels = {
        'not_sent': 'Not Sent',
        'awaiting_signature': 'Awaiting Signature',
        'closed': 'Closed',
        'attention_required': 'Attention Required'
      };
      return labels[status] || status;
    },
    
    getStatusClass(status) {
      const classes = {
        'not_sent': 'status-not-sent',
        'awaiting_signature': 'status-awaiting',
        'closed': 'status-closed',
        'attention_required': 'status-attention',
        'active': 'status-active',
        'completed': 'status-completed'
      };
      return classes[status] || '';
    },
    
    getPaymentRowClass(payment) {
      if (!payment.act || !payment.act.is_sent) {
        return 'payment-row-warning';
      }
      if (payment.act && payment.act.is_sent && !payment.act.is_signed) {
        return 'payment-row-info';
      }
      return '';
    }
  }
};
</script>

<style>
.dashboard-container {
  padding: 20px;
  font-family: Arial, sans-serif;
}

.filters-section {
  background: #f5f5f5;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

.filter-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.filter-group label {
  margin-bottom: 5px;
  font-weight: bold;
}

.filter-group select,
.filter-group input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.filter-group button {
  padding: 8px 16px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 20px;
}

.filter-group button:hover {
  background: #45a049;
}

.summary-section {
  margin-bottom: 30px;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.summary-card {
  background: white;
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 15px;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.summary-card h3 {
  margin: 0 0 10px 0;
  font-size: 14px;
  color: #666;
}

.summary-card .amount {
  font-size: 24px;
  font-weight: bold;
  color: #2196F3;
  margin: 0;
}

.summary-card .count {
  font-size: 24px;
  font-weight: bold;
  color: #4CAF50;
  margin: 0;
}

.summary-card .warning {
  color: #FF9800;
}

.summary-card .danger {
  color: #f44336;
}

.projects-section,
.payments-section {
  margin-bottom: 30px;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.data-table th,
.data-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
}

.data-table th {
  background-color: #f8f9fa;
  font-weight: bold;
}

.data-table tr:hover {
  background-color: #f5f5f5;
}

.status-not-sent {
  color: #f44336;
  font-weight: bold;
}

.status-awaiting {
  color: #FF9800;
  font-weight: bold;
}

.status-closed {
  color: #4CAF50;
  font-weight: bold;
}

.status-attention {
  color: #9C27B0;
  font-weight: bold;
}

.status-active {
  color: #2196F3;
}

.status-completed {
  color: #4CAF50;
}

.payment-row-warning {
  background-color: #FFF3CD !important;
}

.payment-row-info {
  background-color: #D1ECF1 !important;
}

.action-buttons {
  display: flex;
  gap: 5px;
  flex-wrap: wrap;
}

.action-buttons button {
  padding: 5px 10px;
  border: none;
  border-radius: 3px;
  cursor: pointer;
  font-size: 12px;
  white-space: nowrap;
}

.btn-send {
  background: #2196F3;
  color: white;
}

.btn-sign {
  background: #4CAF50;
  color: white;
}

.btn-comment {
  background: #FF9800;
  color: white;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 5px;
  width: 500px;
  max-width: 90%;
}

.modal-content h3 {
  margin-top: 0;
}

.modal-content textarea {
  width: 100%;
  margin: 10px 0;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.modal-actions button {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.modal-actions button:first-child {
  background: #4CAF50;
  color: white;
}

.modal-actions button:last-child {
  background: #f44336;
  color: white;
}
</style>