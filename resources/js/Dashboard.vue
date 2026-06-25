<template>
  <div>
    <h1>Dashboard</h1>

    <div>
      <p>Total: {{ summary.total_amount }}</p>
      <p>Projects: {{ summary.projects_count }}</p>
      <p>Payments: {{ summary.payments_count }}</p>
    </div>

    <hr />

    <h2>Payments</h2>

    <table border="1">
      <tr>
        <th>Date</th>
        <th>Amount</th>
        <th>Project</th>
        <th>Purpose</th>
        <th>Act Status</th>
      </tr>

      <tr v-for="p in payments" :key="p.id">
        <td>{{ p.payment_date }}</td>
        <td>{{ p.amount }}</td>
        <td>{{ p.project.name }}</td>
        <td>{{ p.payment_purpose }}</td>
        <td>{{ p.act?.status }}</td>
      </tr>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      summary: {},
      payments: [],
      projects: []
    };
  },

  async mounted() {
    const res = await axios.get("/api/dashboard");

    this.summary = res.data.summary;
    this.payments = res.data.payments;
    this.projects = res.data.projects;
  }
};
</script>