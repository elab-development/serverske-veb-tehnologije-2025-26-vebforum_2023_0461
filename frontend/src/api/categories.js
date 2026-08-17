import client from "./client";

export function listCategories(params = {}) {
  return client.get("/categories", { params }).then((res) => res.data);
}

export function getCategory(id) {
  return client.get(`/categories/${id}`).then((res) => res.data);
}

export function listCategoryTopics(id) {
  return client.get(`/categories/${id}/topics`).then((res) => res.data);
}

export function createCategory(data) {
  return client.post("/categories", data).then((res) => res.data);
}

export function updateCategory(id, data) {
  return client.put(`/categories/${id}`, data).then((res) => res.data);
}

export function deleteCategory(id) {
  return client.delete(`/categories/${id}`).then((res) => res.data);
}
