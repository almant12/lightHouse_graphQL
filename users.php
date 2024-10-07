
<?php
public static function addFilters(Builder $query, $args = [])
    {
        if (!isset($args["store"]) || !$args["store"]) {
            static::addForcedFilters($query, $args);
        }

        if (!isset($args["filters"])) {
            return;
        }

        // $start = microtime(true);

        foreach ($args["filters"] as $filterName => $filter) {
            if (
                is_null($filter) ||
                (is_array($filter) && count($filter) === 0) ||
                $filter === ""
            ) {
                continue;
            }
            if (gettype($filter) === "string") {
                trim($filter);
            }

            switch ($filterName) {
                case "id":
                    $query->whereIn("users.id", $filter);
                    break;
                case "search":
                    $query->where(function ($query) use ($filter) {
                        $query->where(function ($query) use ($filter) {
                            // E-mail.
                            $query->where("email", "like", "%" . $filter . "%");

                            // First and last name.
                            $query->orWhere(
                                DB::raw("CONCAT(first_name, ' ', last_name)"),
                                "like",
                                "%" . $filter . "%"
                            );
                        });

                        // Profile fields.
                        $query->orWhereHas("profile", function (
                            Builder $query
                        ) use ($filter) {
                            // City.
                            $query->whereHas("city", function ($query) use (
                                $filter
                            ) {
                                $query->where(
                                    "name",
                                    "like",
                                    "%" . $filter . "%"
                                );
                            });

                            // Contacts.
                            // @NOTE For performance reasons, we need to retrieve the ids outside whereHas()
                            // and then use them inside it.
                            $matchedContacts = Contact::where(function (
                                $query
                            ) use ($filter) {
                                $query->where(
                                    "value",
                                    "like",
                                    "%" . $filter . "%"
                                );
                            })
                                ->pluck("id")
                                ->toArray();
                            $query->orWhereHas("contacts", function (
                                $query
                            ) use ($matchedContacts) {
                                $query->whereIn("id", $matchedContacts);
                            });

                            // Cv fields.
                            $query->orWhereHas("cvs", function (
                                Builder $query
                            ) use ($filter) {
                                // Title.
                                $query->where(
                                    "title",
                                    "like",
                                    "%" . $filter . "%"
                                );

                                // Description.
                                $query->orWhere(
                                    "description",
                                    "like",
                                    "%" . $filter . "%"
                                );

                                // Skills.
                                $matchedSkills = Skill::where(
                                    "name",
                                    "like",
                                    "%" . $filter . "%"
                                )
                                    ->pluck("id")
                                    ->toArray();
                                $query->orWhereHas("skills", function (
                                    $query
                                ) use ($matchedSkills) {
                                    $query->whereIn(
                                        "skills.id",
                                        $matchedSkills
                                    );
                                });

                                // Experiences.
                                // @NOTE For performance reasons, we need to retrieve the ids outside whereHas()
                                // and then use them inside it.
                                $matchedExperiences = Experience::where(
                                    function ($query) use ($filter) {
                                        //                                        $query->where(
                                        //                                            "customer",
                                        //                                            "like",
                                        //                                            "%" . $filter . "%"
                                        //                                        );
                                        $query->orWhere(
                                            "position",
                                            "like",
                                            "%" . $filter . "%"
                                        );
                                        $query->orWhere(
                                            "technologies",
                                            "like",
                                            "%" . $filter . "%"
                                        );
                                        $query->orWhere(
                                            "description",
                                            "like",
                                            "%" . $filter . "%"
                                        );
                                    }
                                )
                                    ->pluck("id")
                                    ->toArray();
                                $query->orWhereHas("experiences", function (
                                    $query
                                ) use ($matchedExperiences) {
                                    $query->whereIn("id", $matchedExperiences);
                                });

                                // Qualifications.
                                // @NOTE For performance reasons, we need to retrieve the ids outside whereHas()
                                // and then use them inside it.
                                $matchedQualifications = Qualification::where(
                                    function ($query) use ($filter) {
                                        $query->where(
                                            "title",
                                            "like",
                                            "%" . $filter . "%"
                                        );
                                        $query->orWhere(
                                            "description",
                                            "like",
                                            "%" . $filter . "%"
                                        );
                                    }
                                )
                                    ->pluck("id")
                                    ->toArray();
                                $query->orWhereHas("qualifications", function (
                                    $query
                                ) use ($matchedQualifications) {
                                    $query->whereIn(
                                        "id",
                                        $matchedQualifications
                                    );
                                });

                                // Industries.
                                // @NOTE For performance reasons, we need to retrieve the ids outside whereHas()
                                // and then use them inside it.
                                $matchedIndustries = Industry::where(function (
                                    $query
                                ) use ($filter) {
                                    $query->where(
                                        "name",
                                        "like",
                                        "%" . $filter . "%"
                                    );
                                })
                                    ->pluck("id")
                                    ->toArray();
                                $query->orWhereHas("industries", function (
                                    $query
                                ) use ($matchedIndustries) {
                                    $query->whereIn("id", $matchedIndustries);
                                });

                                // Languages.
                                // @NOTE For performance reasons, we need to retrieve the ids outside whereHas()
                                // and then use them inside it.
                                $matchedLanguages = Language::where(function (
                                    $query
                                ) use ($filter) {
                                    $query->where(
                                        "name",
                                        "like",
                                        "%" . $filter . "%"
                                    );
                                })
                                    ->pluck("code")
                                    ->toArray();
                                $query->orWhereHas("languages", function (
                                    $query
                                ) use ($matchedLanguages) {
                                    $query->whereIn("code", $matchedLanguages);
                                });

                                // Working Roles.
                                // @NOTE For performance reasons, we need to retrieve the ids outside whereHas()
                                // and then use them inside it.
                                $matchedWorkingRoles = WorkingRole::where(
                                    function ($query) use ($filter) {
                                        $query->where(
                                            "name",
                                            "like",
                                            "%" . $filter . "%"
                                        );
                                    }
                                )
                                    ->pluck("id")
                                    ->toArray();
                                $query->orWhereHas("workingRoles", function (
                                    $query
                                ) use ($matchedWorkingRoles) {
                                    $query->whereIn("id", $matchedWorkingRoles);
                                });
                            });
                        });

                        // InvoiceInfo fields.
                        // @NOTE For performance reasons, we need to retrieve the ids outside whereHas()
                        // and then use them inside it.
                        $matchedInvoiceInfos = InvoiceInfo::where(function (
                            $query
                        ) use ($filter) {
                            $query->where(
                                "full_legal_company_name",
                                "like",
                                "%" . $filter . "%"
                            );
                            $query->orWhere(
                                "company_registration_no",
                                "like",
                                "%" . $filter . "%"
                            );
                        })
                            ->pluck("id")
                            ->toArray();
                        $query->orWhereHas("invoiceInfo", function (
                            $query
                        ) use ($matchedInvoiceInfos) {
                            $query->whereIn("id", $matchedInvoiceInfos);
                        });
                    });
                    break;
                case "employerSearch":
                    $query->whereHas("profile", function ($query) use (
                        $filter
                    ) {
                        $query->whereHas("cvs", function ($query) use (
                            $filter
                        ) {
                            $matchedExperiences = Experience::where(function (
                                $query
                            ) use ($filter) {
                                $query->where(
                                    "customer",
                                    "like",
                                    "%" . $filter . "%"
                                );
                            })
                                ->pluck("id")
                                ->toArray();
                            $query->whereHas("experiences", function (
                                $query
                            ) use ($matchedExperiences) {
                                $query->whereIn("id", $matchedExperiences);
                            });
                        });
                    });
                    break;

                case "skills":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        return $query->whereHas("cvs", function (
                            Builder $query
                        ) use ($filter) {
                            foreach ($filter as $value) {
                                $query->whereHas("skills", function (
                                    Builder $query
                                ) use ($value) {
                                    $query->where(function ($query) use (
                                        $value
                                    ) {
                                        $query->where(
                                            "skills.id",
                                            $value["id"]
                                        );
                                        if (isset($value["pivot"])) {
                                            foreach (
                                                $value["pivot"]
                                                as $field => $fieldValue
                                            ) {
                                                $conditionSign =
                                                    gettype($fieldValue) ===
                                                    "integer"
                                                        ? ">="
                                                        : "=";
                                                $query->where(
                                                    "skill_usages.$field",
                                                    $conditionSign,
                                                    $fieldValue
                                                );
                                            }
                                        }
                                    });
                                });
                            }
                        });
                    });
                    break;

                case "roles":
                    $query->whereHas("roles", function (Builder $query) use (
                        $filter
                    ) {
                        $field = !!intval($filter[0]["id"]) ? "id" : "name";
                        $query->whereIn(
                            $field,
                            array_map(function ($v) {
                                return $v["id"];
                            }, $filter)
                        );
                    });
                    break;

                case "statuses":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $query->whereIn(
                            "status_id",
                            array_map(function ($v) {
                                return $v["id"];
                            }, $filter)
                        );
                    });
                    break;

                case "cities":
                    $ids = array_map(function ($v) {
                        return $v["id"];
                    }, $filter);
                    $nullable = in_array(null, $ids);
                    $valuesToRemove = [null];
                    $ids = array_values(array_diff($ids, $valuesToRemove));
                    if ($nullable) {
                        $query->where(function ($query) use ($ids) {
                            $query
                                ->whereIn(
                                    "profiles.city_id", $ids
                                )
                                ->orWhereNull("profiles.city_id");
                        });
                    } else {
                        $query->whereIn(
                            "profiles.city_id",
                            array_map(function ($v) {
                                return $v["id"];
                            }, $filter)
                        );
                    }
                    break;

                case "consultantManagers":
                    $ids = array_map(function ($v) {
                        return $v["id"];
                    }, $filter);
                    $nullable = in_array(null, $ids);
                    $valuesToRemove = [null];
                    $ids = array_values(array_diff($ids, $valuesToRemove));
                    if ($nullable) {
                        $query->where(function ($query) use ($ids) {
                            $query
                                ->whereIn("consultant_manager_id", $ids)
                                ->orWhereNull("consultant_manager_id");
                        });
                    } else {
                        $query->whereIn("consultant_manager_id", $ids);
                    }

                    break;

                case "workingRoles":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $query->whereHas("cvs", function (Builder $query) use (
                            $filter
                        ) {
                            $query->whereHas("workingRoles", function (
                                Builder $query
                            ) use ($filter) {
                                $query->whereIn(
                                    "working_roles.id",
                                    array_map(function ($v) {
                                        return $v["id"];
                                    }, $filter)
                                );
                            });
                        });
                    });
                    break;

                case "industries":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $query->whereHas("cvs", function (Builder $query) use (
                            $filter
                        ) {
                            $query->whereHas("industries", function (
                                Builder $query
                            ) use ($filter) {
                                $query->whereIn(
                                    "id",
                                    array_map(function ($v) {
                                        return $v["id"];
                                    }, $filter)
                                );
                            });
                        });
                    });
                    break;

                case "yearsOfExperience":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $query->whereHas("cvs", function (Builder $query) use (
                            $filter
                        ) {
                            $query->whereHas("workingTerm", function (
                                Builder $query
                            ) use ($filter) {
                                $query->whereIn(
                                    "id",
                                    array_map(function ($v) {
                                        return $v["id"];
                                    }, $filter)
                                );
                            });
                        });
                    });
                    break;

                case "workingLocations":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $query->whereHas("workingLocations", function (
                            $query
                        ) use ($filter) {
                            $query->whereIn(
                                "working_locations.id",
                                array_map(function ($v) {
                                    return $v["id"];
                                }, $filter)
                            );
                        });
                    });
                    break;

                case "terms":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $filter = filter_var($filter, FILTER_VALIDATE_BOOLEAN);
                        $query->whereHas("policies", function (
                            Builder $query
                        ) use ($filter) {
                            $query->where("policy_usages.sent", $filter);
                        });
                        if (!$filter) {
                            $query->orWhereDoesntHave("policies");
                        }
                    });
                    break;

                case "countries":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $query->whereIn(
                            "country",
                            array_map(function ($v) {
                                return $v["code"];
                            }, $filter)
                        );
                    });
                    break;

                case "nationalities":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        $query->whereIn(
                            "nationality",
                            array_map(function ($v) {
                                return $v["code"];
                            }, $filter)
                        );
                    });
                    break;

                case "available":
                    $query->whereDate(
                        "profiles.availability_date",
                        "<=",
                        $filter
                    );
                    break;

                case "partners":
                    $query->whereIn(
                        "profiles.partner_id",
                        array_map(function ($v) {
                            return $v["id"];
                        }, $filter)
                    );
                    break;

                case "userStatus":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter
                    ) {
                        foreach ($filter as $key => $filterItem) {
                            $field = Str::snake($filterItem["value"]);
                            if (Schema::hasColumn("profiles", $field)) {
                                if ($key === 0) {
                                    $query->where($field, true);
                                } else {
                                    $query->orWhere($field, true);
                                }
                            }
                        }
                    });
                    break;

                case "accountStatus":
                    $query->where(function ($query) use ($filter) {
                        $filterValues = array_filter($filter, function ($v) {
                            return $v["value"] !== "deleted";
                        });
                        if (count($filterValues) > 0) {
                            $query->whereIn(
                                "profiles.account_status",
                                array_map(
                                    function ($v) use ($filterValues) {
                                        return $v["value"];
                                    },
                                    array_filter($filterValues, function ($v) {
                                        return $v["value"] !== "deleted";
                                    })
                                )
                            );
                        }
                        $deletedFilter = array_filter($filter, function ($v) {
                            return $v["value"] === "deleted";
                        });
                        if (count($deletedFilter) > 0) {
                            if (count($filterValues) > 0) {
                                $query->orWhereNotNull("profiles.deleted_at");
                            } else {
                                $query->whereNotNull("profiles.deleted_at");
                            }
                        }
                    });
                    break;

                case "priority":
                    $query->whereIn(
                        "profiles.priority",
                        array_map(function ($v) {
                            return $v["id"];
                        }, $filter)
                    );
                    break;

                case "excludeByProfileMatch":
                    $query->whereDoesntHave("profile.profileMatches", function (
                        $query
                    ) use ($filter) {
                        array_map(function ($v) use ($query) {
                            $query->where("usage_id", $v["usage_id"]);
                            $query->where("usage_type", $v["usage_type"]);
                        }, $filter);
                    });
                    break;

                case "projectsByCustomer":
                    $query->whereHas("projects", function ($query) use (
                        $filter
                    ) {
                        $query->whereIn(
                            "customer_id",
                            array_map(function ($v) {
                                return $v["customer_id"];
                            }, $filter)
                        );
                    });
                    break;

                case "excludeProjectsByCustomer":
                    $query->where(function ($query) use ($filter) {
                        $query->whereDoesntHave("projects");
                        $query->orWhereNotIn(
                            "customer_id",
                            array_map(function ($v) {
                                return $v["id"];
                            }, $filter)
                        );
                    });
                    break;

                case "hasPresentableCv":
                    $query->whereHas("profile", function (Builder $query) use (
                        $filter,
                        $args
                    ) {
                        $query->where("include_anonymous_listing", true);
                        $query->whereHas("cvs", function ($query) use (
                            $filter,
                            $args
                        ) {
                            $query->whereNotNull("title");
                            $query->where("title", "!=", "");
                            if (!isset($args["filters"]["workingRoles"])) {
                                $query->has("workingRoles");
                            }
                            if (!isset($args["filters"]["skills"])) {
                                $query->has("skills");
                            }
                            $query->has("experiences");
                        });
                    });

                    break;
            }
        }
    }