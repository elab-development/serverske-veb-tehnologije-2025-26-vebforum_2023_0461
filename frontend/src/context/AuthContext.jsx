import { createContext, useContext, useEffect, useState } from "react";
import * as authApi from "../api/auth";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [token, setToken] = useState(() => localStorage.getItem("token"));
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!token) {
      setLoading(false);
      return;
    }

    authApi
      .getCurrentUser()
      .then((data) => setUser(data))
      .catch(() => {
        setToken(null);
        localStorage.removeItem("token");
      })
      .finally(() => setLoading(false));
  }, [token]);

  function applySession(data) {
    localStorage.setItem("token", data.token);
    setToken(data.token);
    setUser(data.user);
  }

  async function login(credentials) {
    const data = await authApi.login(credentials);
    applySession(data);
    return data;
  }

  async function register(payload) {
    const data = await authApi.register(payload);
    applySession(data);
    return data;
  }

  async function logout() {
    try {
      await authApi.logout();
    } catch {
      // token might already be invalid - clear local state regardless
    }
    localStorage.removeItem("token");
    setToken(null);
    setUser(null);
  }

  function refreshUser() {
    return authApi.getCurrentUser().then((data) => {
      setUser(data);
      return data;
    });
  }

  const value = {
    token,
    user,
    loading,
    isAuthenticated: Boolean(token),
    isAdmin: user?.role === "admin",
    isModerator: user?.role === "moderator" || user?.role === "admin",
    login,
    register,
    logout,
    refreshUser,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return ctx;
}
