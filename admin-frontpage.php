<div id="aa_vue">

  <v-app>

    <v-main>
      <v-alert dense text v-bind:type="alert.type" max-width="900" v-if="alert.display">
        <span v-if="alert.action === 'user_confirmed'">Žádost byla schválena, na e-mail <strong>{{selectedUser.user_email}}</strong> bylo odesláno potvrzení o registraci.</span>
        <span v-else-if="alert.action === 'user_deleted'">Uživatel <strong>{{selectedUser.user_email}}</strong> byl odstraněn z databáze.</span>
        <span v-else-if="alert.action === 'empty_request_list'">Seznam žádostí je prázdný.</span>
        <span v-else>Problém s připojením k databázi.</span>
      </v-alert>
      <h2>Žádosti o registraci</h2>
      <v-card max-width="900">
        <v-simple-table fixed-header height="300px">
          <template v-slot:default loading loadingText="text">
            <thead>
              <tr>
                <th>#</th>
                <th class="text-left">
                  Jméno
                </th>
                <th class="text-left">
                  Příjmení
                </th>
                <th class="text-left">
                  Oddělení
                </th>
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
              <tr v-if="isLoading">
                <td colspan="100%" style="text-align:center; padding:1.8rem">načítám...</td>
              </tr>
              <tr v-else-if="!membershipRequests.length">
                <td colspan="100%" style="text-align:center; padding:1.8rem">nenalezeno</td>
              </tr>
              <tr v-for="(item, index) in membershipRequests" :key="item.user_id">
                <th>{{index +1}}</th>
                <td>{{ item.first_name }}</td>
                <td>{{ item.last_name }}</td>
                <td>{{ item.department }}</td>
                <td>{{ item.user_email }}</td>
                <td>{{ item.user_registered }}</td>
                <td>
                  <v-btn class="pa-2" :disabled="isLoading" color="success" rounded @click="respondToUserRequest(item.user_id, item.user_email, 'confirm', index)">
                    Potvrdit
                  </v-btn>
                  <v-btn class="pa-2 ml-2" :disabled="isLoading" color="error" rounded @click="respondToUserRequest(item.user_id, item.user_email, 'cancel', index)">
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
        membershipRequests: [],
        isLoading: true,
        alert: {
          display: false,
          type: null
        },
        timeoutNotification: null
      }
    },
    methods: {
      removeItemsFromArray: function(items, from, to) {
        const rest = items.slice((to || from) + 1 || items.length);
        items.length = from < 0 ? items.length + from : from;
        return items.push.apply(items, rest);
      },

      respondToUserRequest: function(user_id, user_email, action, itemIndex) {

        this.selectedUser = {
          user_email: user_email
        }

        if (!confirm("Potvrďte akci")) return false

        this.isLoading = true

        fetch(wpRestApi.root + "/user_registration_request", {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json;charset=UTF-8',
              'X-WP-Nonce': wpRestApi.nonce,
            },
            body: JSON.stringify({
              action: action,
              user_id: parseInt(user_id)
            })
          }).then(function(response) {
            if (response.ok) {
              return response.json()
            }

            return Promise.reject(response);
          })
          .then(function(data) {
            this.alert = {
              display: true,
              type: action === "confirm" ? "success" : "warning",
              action: action === "confirm" ? "user_confirmed" : "user_deleted"
            }

            this.isLoading = false
            const updatedList = this.removeItemsFromArray(this.membershipRequests, itemIndex)
            this.membershipRequests = updatedList

          }.bind(this))
          .catch(function(error) {
            this.alert = {
              display: true,
              type: "error",
              action: null
            }

            this.isLoading = false
          }.bind(this))
      }
    },

    watch: {
      alert: function() {
        if (this.timeoutNotification) {
          clearTimeout(this.timeoutNotification)
        }
        this.timeoutNotification = setTimeout(function() {
          return this.alert = {
            display: false,
            type: null
          }
        }.bind(this), 3400)
      }
    },

    mounted() {

      fetch(wpRestApi.root + "/get_user_list", {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-WP-Nonce': wpRestApi.nonce,
          },
        })
        .then((response) => {
          if (response.ok) {
            return response.json()
          }
          return Promise.reject(response)
        })
        .then(function(data) {
          this.isLoading = false
          this.membershipRequests = data
        }.bind(this))
        .catch(function(error) {

          error.json().then(function(response) {



            setTimeout(function() {

              this.alert = (response.code === "rest_not_found")
              ?
                {
                  display: true,
                  type: "success",
                  action: "empty_request_list"
                } 
              :
                {
                  display: true,
                  type: "error",
                  action: null
                }


              return this.isLoading = false
            }.bind(this), 2000)




          }.bind(this))


        }.bind(this))
    }
  });
</script>

<style scoped>
  #aa_vue {
    margin-top: 2rem;
  }

  .v-application {
    background-color: #f1f1f1 !important;
  }
</style>