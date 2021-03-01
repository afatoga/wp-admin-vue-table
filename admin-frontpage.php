<div id="aa_vue">

  <v-app id="inspire">
  <v-alert
      dense
      text
      type="success"
      max-width="900"
    >
      Žádost byla schválena, na e-mail  <strong>petr.kubek@gmail.com</strong> bylo odesláno potvrzení registrace.
    </v-alert>
  <v-main>
    <h2>Žádosti o registraci</h2>
    <v-card max-width="900">
      <!-- <v-card-title>
        <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
      </v-card-title> -->
      <v-simple-table fixed-header height="300px">
        <template v-slot:default loading loadingText="text">
          <thead>
            <tr>
            <th>#</th>
              <th class="text-left">
                E-mail
              </th>
              <th class="text-left">
                Datum žádosti
              </th>
              <th>
                Akce</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in membershipRequests" :key="item.ID">
            <th>{{item.ID}}</th>
              <td>{{ item.user_email }}</td>
              <td>{{ item.user_registered }}</td>
              <td>
                <v-btn class="pa-2" :disabled="isLoading" color="success" rounded @click="confirmUserRequest(item.ID)">
                  Potvrdit
                </v-btn>
                <v-btn class="pa-2 ml-2" :disabled="isLoading" color="error" rounded>
                  Odmítnout
                </v-btn>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <!-- <v-data-table :headers="headers" :items="membershipRequests" :search="search"></v-data-table> -->
    </v-card>
    </v-main>
  </v-app>

</div>

<script>
  new Vue({
    el: "#aa_vue",
    vuetify: new Vuetify(),
    data() {
      return {
        //search: '',
        membershipRequests: [],
        isLoading: true,

      }
    },
    methods: {
      confirmUserRequest: function(userId) {
        if (!confirm("Potvrďte akci")) return false

        this.isLoading = true

        fetch(wpRestApi.root + "aa_restserver/v1/create_user_acc", {
            method: 'POST',
            body: JSON.stringify({
              _wpnonce: wpRestApi.nonce
            })
          }).then(response => {
            if (response.ok) {
              return response
            }
            // convert non-2xx HTTP responses into errors:
            const error = new Error(response.statusText)
            error.response = response
            return Promise.reject(error)
          })
          .then(response => response.json())
          .then(data => {
            this.membershipRequests = data
            this.isLoading = false
          })
      }
    },

    mounted() {
      fetch(wpRestApi.root + "aa_restserver/v1/get_user_list?_wpnonce=" + wpRestApi.nonce, {
          method: 'GET'
        })

        .then(response => {
          if (response.ok) {
            return response
          }
          // convert non-2xx HTTP responses into errors:
          const error = new Error(response.statusText)
          error.response = response
          return Promise.reject(error)
        })
        .then(response => response.json())
        .then(data => {
          this.membershipRequests = data
          this.isLoading = false
        })
    }
  });
</script>

<style scoped>
  #aa_vue {
    margin-top:2rem;
  }
  .v-application {
    background-color: #f1f1f1 !important;
  }
</style>