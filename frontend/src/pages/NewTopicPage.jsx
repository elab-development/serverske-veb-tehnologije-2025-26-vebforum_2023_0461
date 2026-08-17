import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import * as topicsApi from "../api/topics";
import * as categoriesApi from "../api/categories";
import { ErrorMessage } from "../components/StatusMessage";

export default function NewTopicPage() {
  const navigate = useNavigate();
  const [categories, setCategories] = useState([]);
  const [form, setForm] = useState({ title: "", body: "", category_id: "" });
  const [error, setError] = useState(null);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    categoriesApi
      .listCategories()
      .then((res) => {
        setCategories(res.data);
        if (res.data.length > 0) {
          setForm((f) => ({ ...f, category_id: res.data[0].id }));
        }
      })
      .catch(() => {});
  }, []);

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      const res = await topicsApi.createTopic(form);
      navigate(`/topics/${res.data.id}`);
    } catch (err) {
      setError(err);
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="page">
      <h1>Nova tema</h1>
      <form onSubmit={handleSubmit} className="form">
        <label>
          Kategorija
          <select
            required
            value={form.category_id}
            onChange={(e) =>
              setForm({ ...form, category_id: e.target.value })
            }
          >
            {categories.map((category) => (
              <option key={category.id} value={category.id}>
                {category.name}
              </option>
            ))}
          </select>
        </label>
        <label>
          Naslov
          <input
            required
            maxLength={200}
            value={form.title}
            onChange={(e) => setForm({ ...form, title: e.target.value })}
          />
        </label>
        <label>
          Sadržaj
          <textarea
            required
            rows={6}
            value={form.body}
            onChange={(e) => setForm({ ...form, body: e.target.value })}
          />
        </label>
        <ErrorMessage error={error} />
        <button type="submit" disabled={submitting}>
          {submitting ? "Objavljivanje…" : "Objavi temu"}
        </button>
      </form>
    </div>
  );
}
