import client from "./client";

export function listUsers(params = {}) {
  return client.get("/users", { params }).then((res) => res.data);
}

export function updateUserRole(id, role) {
  return client.put(`/users/${id}/role`, { role }).then((res) => res.data);
}
